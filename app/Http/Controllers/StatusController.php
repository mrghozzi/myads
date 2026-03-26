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

        $request->merge([
            'directory_category_id' => $this->normalizeDirectoryCategoryId($request->input('directory_category_id')),
            'publish_mode' => $this->normalizePublishMode($request->input('publish_mode')),
        ]);

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
            'publish_mode' => 'nullable|in:post,directory_only',
            'save_to_directory' => 'nullable|boolean',
            'directory_name' => 'nullable|string|max:255',
            'directory_category_id' => 'nullable|integer|exists:cat_dir,id',
            'directory_tags' => 'nullable|string|max:255',
            'repost_status_id' => 'nullable|integer|exists:status,id',
        ]);

        $text = trim((string) $request->input('text', $request->input('txt', '')));
        $time = time();
        $statusType = $postKind === 'gallery' ? 4 : 100;
        $publishMode = (string) $request->input('publish_mode', 'post');
        $linkUrl = $this->resolveLinkUrl($request, $text);
        $legacyDirectorySave = $request->boolean('save_to_directory');

        $hasMentions = ContentFormatter::extractMentionUsernames($text) !== [];

        if (($postKind === 'link' || $legacyDirectorySave || $publishMode === 'directory_only') && !$schema->supports('link_previews')) {
            return back()
                ->with('error', $schema->blockedActionMessage('link_previews', __('messages.link_previews')))
                ->withInput();
        }

        if ($linkUrl && !$schema->supports('link_previews')) {
            $linkUrl = null;
        }

        if ($publishMode === 'directory_only') {
            if (!$linkUrl) {
                return back()
                    ->with('error', __('messages.directory_only_requires_link'))
                    ->withInput();
            }

            if ($this->requestHasGallerySelection($request) || $postKind === 'repost') {
                return back()
                    ->with('error', __('messages.directory_only_incompatible_post_type'))
                    ->withInput();
            }

            DB::beginTransaction();
            try {
                $preview = $linkPreviewService->fetch($linkUrl);
                [$directory] = $this->createDirectoryEntry($request, $user, $preview, $text, $time);

                DB::commit();
                return redirect()->route('directory.show', $directory->id);
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->with('error', $e->getMessage())->withInput();
            }
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

                if ($legacyDirectorySave) {
                    [$directory, $directoryStatus] = $this->createDirectoryEntry($request, $user, $preview, $text, $time);

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
        $destinationPath = base_path('upload');

        if (!is_dir($destinationPath) && !mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
            throw new \RuntimeException('Unable to create gallery attachment directory.');
        }

        $file->move($destinationPath, $filename);
        return 'upload/' . $filename;
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

    private function normalizeDirectoryCategoryId(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === '0' || $value === 0) {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function normalizePublishMode(mixed $value): string
    {
        return in_array($value, ['post', 'directory_only'], true) ? (string) $value : 'post';
    }

    private function resolveLinkUrl(Request $request, string $text): ?string
    {
        $value = trim((string) $request->input('link_url', ''));

        if ($value !== '') {
            return $value;
        }

        return $this->extractFirstUrl($text);
    }

    private function requestHasGallerySelection(Request $request): bool
    {
        if (!empty($request->file('images', []))) {
            return true;
        }

        return is_string($request->input('img')) && trim((string) $request->input('img')) !== '';
    }

    public function addGalleryImages(Request $request, int $topicId)
    {
        $user = Auth::user();
        $topic = ForumTopic::findOrFail($topicId);

        if ($topic->uid !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|max:10000',
        ]);

        $currentCount = ForumAttachment::where('topic_id', $topicId)->count();
        if ($currentCount + count($request->file('images')) > 10) {
            return response()->json(['success' => false, 'message' => __('messages.gallery_limit_exceeded')], 422);
        }

        DB::beginTransaction();
        try {
            $this->storeGalleryAssets($topic, $request, $user->id);
            $this->syncStatusType($topicId);
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteGalleryImage(Request $request, int $attachmentId)
    {
        $user = Auth::user();
        $attachment = ForumAttachment::findOrFail($attachmentId);
        $topicId = $attachment->topic_id;
        $topic = ForumTopic::findOrFail($topicId);

        if ($topic->uid !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        DB::beginTransaction();
        try {
            $this->deleteAttachmentFile($attachment);
            $attachment->delete();

            $this->syncStatusType($topicId);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function clearGallery(Request $request, int $topicId)
    {
        $user = Auth::user();
        $topic = ForumTopic::findOrFail($topicId);

        if ($topic->uid !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        DB::beginTransaction();
        try {
            $attachments = ForumAttachment::where('topic_id', $topicId)->get();
            foreach ($attachments as $attachment) {
                $this->deleteAttachmentFile($attachment);
                $attachment->delete();
            }

            $this->syncStatusType($topicId);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reorderGalleryImages(Request $request, int $topicId)
    {
        $user = Auth::user();
        $topic = ForumTopic::findOrFail($topicId);

        if ($topic->uid !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->input('order') as $index => $attachmentId) {
                ForumAttachment::where('id', $attachmentId)
                    ->where('topic_id', $topicId)
                    ->update(['sort_order' => $index]);
            }

            $this->syncStatusType($topicId);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function deleteAttachmentFile(ForumAttachment $attachment): void
    {
        $path = public_path($attachment->file_path);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    private function syncStatusType(int $topicId): void
    {
        $attachments = ForumAttachment::where('topic_id', $topicId)->get();
        $status = Status::where('tp_id', $topicId)->first();

        if (!$status) return;

        if ($attachments->isEmpty()) {
            $status->update(['s_type' => 100]);
            Option::where('o_parent', $topicId)->where('o_type', 'image_post')->delete();
        } else {
            $status->update(['s_type' => 4]);
            
            // Ensure Option record matches the first attachment
            $firstAttachment = $attachments->sortBy('sort_order')->first();
            Option::updateOrCreate(
                ['o_parent' => $topicId, 'o_type' => 'image_post'],
                [
                    'name' => (string) time(),
                    'o_valuer' => $firstAttachment->file_path,
                    'o_order' => $firstAttachment->user_id,
                    'o_mode' => 'file',
                ]
            );
        }
    }

    private function createDirectoryEntry(Request $request, $user, array $preview, string $text, int $time): array
    {
        $directory = Directory::create([
            'uid' => $user->id,
            'name' => $request->input('directory_name') ?: ($preview['title'] ?: $preview['domain']),
            'url' => $preview['normalized_url'],
            'txt' => $text !== '' ? $text : ($preview['description'] ?? ''),
            'metakeywords' => (string) $request->input('directory_tags', ''),
            'cat' => (int) ($request->input('directory_category_id') ?? 0),
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

        return [$directory, $directoryStatus];
    }
}
