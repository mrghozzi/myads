<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\ForumAttachment;
use App\Models\ForumTopic;
use App\Models\Group;
use App\Models\Option;
use App\Models\Short;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use App\Services\GamificationService;
use App\Services\GroupAccessService;
use App\Services\GroupMembershipService;
use App\Services\LinkPreviewService;
use App\Services\MentionService;
use App\Services\NotificationService;
use App\Services\SecurityPolicyService;
use App\Services\SecurityThrottleService;
use App\Services\UserPrivacyService;
use App\Services\V420SchemaService;
use App\Services\StatusPostService;
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
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $relativePath = 'upload/' . $filename;
        $url = asset($relativePath);

        return '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"> Uploaded </div> </div>'
            . '<img src="' . $url . '" style="width: 100%; height: auto; border-radius: 12px; margin-top: 24px;"><br>'
            . '<input type="text" name="img" style="visibility:hidden" value="' . e($relativePath) . '">';
    }

    public function linkPreview(
        Request $request,
        LinkPreviewService $linkPreviewService,
        SecurityPolicyService $securityPolicy
    )
    {
        $request->validate([
            'link_url' => 'required|string|max:2048',
        ]);

        if ($violation = $securityPolicy->urlViolation($request->input('link_url'), 'posts')) {
            return response()->json([
                'message' => $violation,
            ], 422);
        }

        return response()->json($linkPreviewService->fetch($request->input('link_url')));
    }

    public function create(Request $request, StatusPostService $statusPostService)
    {
        try {
            $status = $statusPostService->create($request, Auth::user());
            
            if ($status->s_type == 1 && $status->tp_id) {
                if ($request->input('publish_mode') === 'directory_only') {
                     return redirect()->route('directory.show', $status->tp_id);
                }
                if ($request->input('s_type') == 1) {
                     return redirect()->route('directory.show.short', $status->tp_id);
                }
            }
            if ($status->group_id) {
                $group = Group::find($status->group_id);
                return redirect()
                    ->route('groups.show', ['group' => $group->slug, 'tab' => 'feed'])
                    ->with('success', __('messages.groups_post_created'));
            }
            return redirect()->route('forum.topic', $status->tp_id);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Status $status)
    {
        $user = Auth::user();
        if ((int)$status->uid !== (int)$user->id && !$user->isAdmin()) {
            abort(403, __('messages.unauthorized'));
        }
        return view('theme::status.edit', compact('status'));
    }

    public function update(Request $request, Status $status, StatusPostService $statusPostService)
    {
        try {
            $statusPostService->update($request, $status, Auth::user());
            if ($status->group_id) {
                $group = Group::find($status->group_id);
                return redirect()
                    ->route('groups.show', ['group' => $group->slug, 'tab' => 'feed'])
                    ->with('success', __('messages.post_updated_successfully'));
            }
            return redirect()->route('forum.topic', $status->tp_id)->with('success', __('messages.post_updated_successfully'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Status $status, StatusPostService $statusPostService)
    {
        try {
            $statusPostService->delete($status, Auth::user());
            return redirect()->route('portal')->with('success', __('messages.post_deleted_successfully'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function createForumTopic(int $userId, string $text, string $postKind, int $time, ?int $groupId = null): ForumTopic
    {
        return ForumTopic::create([
            'uid' => $userId,
            'name' => $postKind,
            'txt' => $text,
            'cat' => 0,
            'group_id' => $groupId,
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

    private function storeMediaAssets(ForumTopic $topic, Request $request, int $userId, string $kind): void
    {
        $inputName = match ($kind) {
            'video', 'reels' => 'videos',
            'audio', 'music' => 'audios',
            'file' => 'files',
            default => 'files',
        };

        $uploadedFiles = $request->file($inputName, []);
        if ($uploadedFiles instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFiles = [$uploadedFiles];
        }
        
        if (empty($uploadedFiles)) {
            return;
        }

        $paths = [];
        foreach ($uploadedFiles as $file) {
            $paths[] = $this->storeMediaFile($file, $topic->id, $userId, $kind);
        }

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

    /**
     * Allowed file extensions for media uploads (security whitelist).
     */
    private const ALLOWED_MEDIA_EXTENSIONS = [
        // Images
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg',
        // Video
        'mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv',
        // Audio
        'mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a',
        // Documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf',
        // Archives
        'zip', 'rar', '7z', 'tar', 'gz',
    ];

    /**
     * Dangerous MIME type prefixes that must never be stored.
     */
    private const BLOCKED_MIME_PREFIXES = [
        'application/x-httpd-php',
        'application/x-php',
        'text/x-php',
        'application/x-executable',
        'application/x-sharedlib',
    ];

    private function storeMediaFile($file, int $topicId, int $userId, string $kind): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');

        // Security: Block dangerous file extensions
        if (!in_array($extension, self::ALLOWED_MEDIA_EXTENSIONS, true)) {
            throw new \RuntimeException(__('messages.file_type_not_allowed') ?? 'File type not allowed: ' . $extension);
        }

        // Security: Block dangerous MIME types
        $mimeType = strtolower((string) ($file->getClientMimeType() ?: ''));
        foreach (self::BLOCKED_MIME_PREFIXES as $blockedPrefix) {
            if (str_starts_with($mimeType, $blockedPrefix)) {
                throw new \RuntimeException(__('messages.file_type_not_allowed') ?? 'File type not allowed.');
            }
        }

        $filename = $kind . '_' . $topicId . '_' . time() . '_' . Str::random(12) . '.' . $extension;
        $destinationPath = base_path('upload');

        if (!is_dir($destinationPath) && !mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
            throw new \RuntimeException('Unable to create media attachment directory.');
        }

        $file->move($destinationPath, $filename);
        return 'upload/' . $filename;
    }

    private function storeGalleryFile($file, int $topicId, int $userId): string
    {
        return $this->storeMediaFile($file, $topicId, $userId, 'topic');
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
