<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\ForumAttachment;
use App\Models\ForumTopic;
use App\Models\Option;
use App\Models\Short;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use App\Services\GamificationService;
use App\Services\LinkPreviewService;
use App\Services\MentionService;
use App\Services\NotificationService;
use App\Services\UserPrivacyService;
use App\Services\V420SchemaService;
use App\Support\ContentFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatusController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'fimg' => 'required|image|max:10000',
        ]);

        $file = $request->file('fimg');
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(8) . '.' . $extension;

        $destinationPath = base_path('upload');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);
        $relativePath = 'upload/' . $filename;
        $url = asset($relativePath);

        return '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"> Uploaded </div> </div>'
            . '<img src="' . $url . '" style="width: 100%; height: auto; border-radius: 12px; margin-top: 24px;"><br>'
            . '<input type="text" name="img" style="visibility:hidden" value="' . e($relativePath) . '">';
    }

    public function linkPreview(Request $request, LinkPreviewService $linkPreviewService)
    {
        $request->validate([
            'link_url' => 'required|string|max:2048',
        ]);

        return response()->json($linkPreviewService->fetch($request->input('link_url')));
    }

    public function create(
        Request $request,
        LinkPreviewService $linkPreviewService,
        MentionService $mentions,
        NotificationService $notifications,
        UserPrivacyService $privacy,
        GamificationService $gamification
    ) {
        $user = Auth::user();
        $schema = app(V420SchemaService::class);
        $legacyType = (int) $request->input('s_type', 100);
        $postKind = (string) $request->input('post_kind', '');

        if ($postKind === '' && $request->filled('repost_status_id')) {
            $postKind = 'repost';
        }

        if ($postKind === '' && $legacyType === 1) {
            return $this->createLegacyDirectoryPost($request, $user);
        }

        if ($postKind === '') {
            $postKind = match ($legacyType) {
                4 => 'gallery',
                default => 'text',
            };
        }

        $request->validate([
            'text' => 'nullable|string|max:10000',
            'txt' => 'nullable|string|max:10000',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:10000',
            'link_url' => 'nullable|string|max:2048',
            'save_to_directory' => 'nullable|boolean',
            'directory_name' => 'nullable|string|max:255',
            'directory_category_id' => 'nullable|integer|exists:cat_dir,id',
            'directory_tags' => 'nullable|string|max:255',
            'repost_status_id' => 'nullable|integer|exists:status,id',
        ]);

        $text = trim((string) $request->input('text', $request->input('txt', '')));
        $time = time();
        $statusType = $postKind === 'gallery' ? 4 : 100;
        $linkUrl = $request->input('link_url');

        if ($postKind === 'text' && !$linkUrl) {
            $linkUrl = $this->extractFirstUrl($text);
        }

        $hasMentions = ContentFormatter::extractMentionUsernames($text) !== [];

        if (($postKind === 'link' || $request->boolean('save_to_directory')) && !$schema->supports('link_previews')) {
            return back()
                ->with('error', $schema->blockedActionMessage('link_previews', __('messages.link_previews')))
                ->withInput();
        }

        if ($postKind === 'text' && $linkUrl && !$schema->supports('link_previews')) {
            $linkUrl = null;
        }

        if ($postKind === 'repost' && !$schema->supports('reposts')) {
            return back()
                ->with('error', $schema->blockedActionMessage('reposts', __('messages.reposts')))
                ->withInput();
        }

        if ($hasMentions && !$schema->supports('mentions')) {
            return back()
                ->with('error', $schema->blockedActionMessage('mentions', __('messages.mentions')))
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $topic = $this->createForumTopic($user->id, $text, $postKind, $time);
            $status = Status::create([
                'uid' => $user->id,
                'date' => $time,
                's_type' => $statusType,
                'tp_id' => $topic->id,
                'statu' => 1,
            ]);

            if ($postKind === 'gallery') {
                $this->storeGalleryAssets($topic, $request, $user->id);
            }

            if ($linkUrl) {
                $preview = $linkPreviewService->fetch($linkUrl);
                $linkPreview = StatusLinkPreview::create([
                    'status_id' => $status->id,
                    'url' => $preview['url'],
                    'normalized_url' => $preview['normalized_url'],
                    'title' => $preview['title'],
                    'description' => $preview['description'],
                    'image_url' => $preview['image_url'],
                    'site_name' => $preview['site_name'],
                    'domain' => $preview['domain'],
                ]);

                if ($request->boolean('save_to_directory')) {
                    $directory = Directory::create([
                        'uid' => $user->id,
                        'name' => $request->input('directory_name') ?: ($preview['title'] ?: $preview['domain']),
                        'url' => $preview['normalized_url'],
                        'txt' => $text !== '' ? $text : ($preview['description'] ?? ''),
                        'metakeywords' => (string) $request->input('directory_tags', ''),
                        'cat' => (int) $request->input('directory_category_id', 0),
                        'vu' => 0,
                        'statu' => 1,
                        'date' => $time,
                    ]);

                    $directoryStatus = Status::create([
                        'uid' => $user->id,
                        'date' => $time,
                        's_type' => 1,
                        'tp_id' => $directory->id,
                        'statu' => 1,
                    ]);

                    Short::create([
                        'uid' => $user->id,
                        'sho' => hash('crc32', $preview['normalized_url'] . $directory->id),
                        'url' => $preview['normalized_url'],
                        'clik' => 0,
                        'sh_type' => 1,
                        'tp_id' => $directory->id,
                    ]);

                    $linkPreview->update([
                        'directory_id' => $directory->id,
                        'directory_status_id' => $directoryStatus->id,
                    ]);
                }
            }

            if ($postKind === 'repost') {
                $originalStatus = Status::findOrFail((int) $request->input('repost_status_id'));
                $originalOwner = $originalStatus->user()->first();

                if ($originalOwner && !$privacy->canRepost($originalOwner, $user) && (int) $originalOwner->id !== (int) $user->id && !$user->isAdmin()) {
                    throw new \RuntimeException(__('messages.reposts_disabled_for_user'));
                }

                StatusRepost::create([
                    'status_id' => $status->id,
                    'original_status_id' => $originalStatus->id,
                    'user_id' => $user->id,
                ]);

                if ($originalOwner) {
                    $notifications->send(
                        $originalOwner->id,
                        __('messages.repost_notification', ['user' => $user->username]),
                        '/t' . $topic->id,
                        'share',
                        $user->id
                    );
                }
            }

            $mentions->createStatusMentions($user, $status, $text, '/t' . $topic->id);

            $gamification->recordEvent($user->id, 'post_created');
            if ($postKind === 'repost') {
                $gamification->recordEvent($user->id, 'repost_created');
            } else {
                $gamification->refreshBadges($user->id);
            }

            DB::commit();
            return redirect()->route('forum.topic', $topic->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function createLegacyDirectoryPost(Request $request, $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'categ' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            $time = time();
            $dir = Directory::create([
                'uid' => $user->id,
                'name' => $request->name,
                'url' => $request->url,
                'txt' => $request->input('txt', ''),
                'metakeywords' => $request->input('tag', ''),
                'cat' => $request->categ,
                'vu' => 0,
                'statu' => 1,
                'date' => $time,
            ]);

            Status::create([
                'uid' => $user->id,
                'date' => $time,
                's_type' => 1,
                'tp_id' => $dir->id,
                'statu' => 1,
            ]);

            Short::create([
                'uid' => $user->id,
                'sho' => hash('crc32', $request->url . $dir->id),
                'url' => $request->url,
                'clik' => 0,
                'sh_type' => 1,
                'tp_id' => $dir->id,
            ]);

            DB::commit();
            return redirect()->route('directory.show.short', $dir->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function createForumTopic(int $userId, string $text, string $postKind, int $time): ForumTopic
    {
        return ForumTopic::create([
            'uid' => $userId,
            'name' => $postKind,
            'txt' => $text,
            'cat' => 0,
            'statu' => 1,
            'date' => $time,
            'reply' => 0,
            'vu' => 0,
        ]);
    }

    private function storeGalleryAssets(ForumTopic $topic, Request $request, int $userId): void
    {
        $uploadedFiles = $request->file('images', []);
        $legacyPath = $request->input('img');

        if (empty($uploadedFiles) && (!$legacyPath || !is_string($legacyPath))) {
            throw new \RuntimeException(__('messages.gallery_requires_images'));
        }

        $paths = [];
        if ($legacyPath && is_string($legacyPath)) {
            $paths[] = $legacyPath;
        }

        foreach ($uploadedFiles as $file) {
            $paths[] = $this->storeGalleryFile($file, $topic->id, $userId);
        }

        $paths = array_values(array_unique(array_filter($paths)));
        if (count($paths) > 10) {
            throw new \RuntimeException(__('messages.gallery_limit_exceeded'));
        }

        if ($paths === []) {
            throw new \RuntimeException(__('messages.gallery_requires_images'));
        }

        Option::updateOrCreate(
            ['o_parent' => $topic->id, 'o_type' => 'image_post'],
            [
                'name' => (string) time(),
                'o_valuer' => $paths[0],
                'o_order' => $userId,
                'o_mode' => 'file',
            ]
        );

        foreach ($paths as $index => $path) {
            ForumAttachment::create([
                'topic_id' => $topic->id,
                'user_id' => $userId,
                'file_path' => $path,
                'original_name' => basename($path),
                'mime_type' => $this->mimeTypeForPath($path),
                'file_size' => $this->fileSizeForPath($path),
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function storeGalleryFile($file, int $topicId, int $userId): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = 'topic_' . $topicId . '_' . time() . '_' . Str::random(12) . '.' . $extension;
        $destinationPath = storage_path('app/forum_attachments');

        if (!is_dir($destinationPath) && !mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
            throw new \RuntimeException('Unable to create gallery attachment directory.');
        }

        $file->move($destinationPath, $filename);
        return 'forum_attachments/' . $filename;
    }

    private function mimeTypeForPath(string $path): string
    {
        $normalized = ltrim($path, '/\\');
        $storageFile = storage_path('app/' . $normalized);
        $publicFile = public_path($normalized);
        $rootFile = base_path($normalized);

        $file = is_file($storageFile) ? $storageFile : (is_file($publicFile) ? $publicFile : $rootFile);
        return is_file($file) ? (mime_content_type($file) ?: 'image/jpeg') : 'image/jpeg';
    }

    private function fileSizeForPath(string $path): int
    {
        $normalized = ltrim($path, '/\\');
        $storageFile = storage_path('app/' . $normalized);
        $publicFile = public_path($normalized);
        $rootFile = base_path($normalized);

        $file = is_file($storageFile) ? $storageFile : (is_file($publicFile) ? $publicFile : $rootFile);
        return is_file($file) ? (int) filesize($file) : 0;
    }

    private function extractFirstUrl(?string $text): ?string
    {
        if (!preg_match('/\b((https?:\/\/)?[a-z0-9.-]+\.[a-z]{2,}(\/\S*)?)/i', (string) $text, $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }
}
