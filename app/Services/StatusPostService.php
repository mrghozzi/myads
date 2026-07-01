<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\ForumAttachment;
use App\Models\ForumTopic;
use App\Models\Group;
use App\Models\Option;
use App\Models\Short;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatusPostService
{
    public function __construct(
        protected LinkPreviewService $linkPreviewService,
        protected MentionService $mentions,
        protected NotificationService $notifications,
        protected UserPrivacyService $privacy,
        protected GamificationService $gamification,
        protected SecurityPolicyService $securityPolicy,
        protected SecurityThrottleService $securityThrottle,
        protected V420SchemaService $schema
    ) {
    }

    public function create(Request $request, User $user): Status
    {
        $legacyType = (int) $request->input('s_type', 100);
        $postKind = (string) $request->input('post_kind', '');
        $group = null;

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
                10 => 'video',
                11 => 'audio',
                12 => 'file',
                13 => 'music',
                14 => 'clips',
                default => 'text',
            };
        }

        $settings = $this->getUploadSettings();
        $maxSizeKb = $settings['max_upload_size'] * 1024;
        $mimes = $settings['allowed_extensions'];
        $mimetypes = $settings['allowed_mime_types'];

        $rules = [
            'text' => 'nullable|string|max:10000',
            'txt' => 'nullable|string|max:10000',
            'images' => 'nullable|array|max:10',
            'images.*' => "image|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'videos' => 'nullable|array|max:1',
            'videos.*' => "file|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'audios' => 'nullable|array|max:1',
            'audios.*' => "file|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'files' => 'nullable|array|max:5',
            'files.*' => "file|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'link_url' => 'nullable|string|max:2048',
            'publish_mode' => 'nullable|in:post,directory_only',
            'post_kind' => 'nullable|string|in:text,gallery,link,repost,video,audio,file,music,clips',
            'save_to_directory' => 'nullable|boolean',
            'directory_name' => 'nullable|string|max:255',
            'directory_category_id' => 'nullable|integer|exists:cat_dir,id',
            'directory_tags' => 'nullable|string|max:255',
            'repost_status_id' => 'nullable|integer|exists:status,id',
        ];

        if ($this->schema->supports('groups')) {
            $rules['group_id'] = 'nullable|integer|exists:groups,id';
        }

        $request->validate($rules);

        if (!$settings['video_sharing'] && $postKind === 'video') {
            throw new \RuntimeException(__('messages.video_upload_disabled') ?? 'Video upload is disabled.');
        }
        if (!$settings['clips_upload'] && $postKind === 'clips') {
            throw new \RuntimeException(__('messages.clips_upload_disabled') ?? 'Clips upload is disabled.');
        }
        if (!$settings['audio_sharing'] && in_array($postKind, ['audio', 'music'])) {
            throw new \RuntimeException(__('messages.audio_upload_disabled') ?? 'Audio upload is disabled.');
        }
        if (!$settings['file_sharing'] && $postKind === 'file') {
            throw new \RuntimeException(__('messages.file_upload_disabled') ?? 'File upload is disabled.');
        }

        $text = trim((string) $request->input('text', $request->input('txt', '')));
        $time = time();
        $postKind = (string) $request->input('post_kind', $postKind);
        $statusType = match ($postKind) {
            'gallery' => 4,
            'video' => Status::TYPE_VIDEO,
            'audio' => Status::TYPE_AUDIO,
            'file' => Status::TYPE_FILE,
            'music' => Status::TYPE_MUSIC,
            'clips' => Status::TYPE_CLIPS,
            default => 100,
        };
        $publishMode = (string) $request->input('publish_mode', 'post');
        $linkUrl = $this->resolveLinkUrl($request, $text);
        $legacyDirectorySave = $request->boolean('save_to_directory');

        if ($this->schema->supports('groups') && $request->filled('group_id')) {
            $group = Group::findOrFail((int) $request->input('group_id'));
            app(GroupAccessService::class)->ensureCanPostToGroup($group, $user);
        }

        if ($cooldownMessage = $this->securityThrottle->actionMessage($user, 'post')) {
            throw new \RuntimeException($cooldownMessage);
        }

        if ($violation = $this->securityPolicy->textViolation($text, 'posts')) {
            throw new \RuntimeException($violation);
        }

        if ($violation = $this->securityPolicy->urlViolation($linkUrl, 'posts')) {
            throw new \RuntimeException($violation);
        }

        $hasMentions = \App\Support\ContentFormatter::extractMentionUsernames($text) !== [];

        if (($postKind === 'link' || $legacyDirectorySave || $publishMode === 'directory_only') && !$this->schema->supports('link_previews')) {
            throw new \RuntimeException($this->schema->blockedActionMessage('link_previews', __('messages.link_previews')));
        }

        if ($linkUrl && !$this->schema->supports('link_previews')) {
            $linkUrl = null;
        }

        if ($publishMode === 'directory_only') {
            if ($group) {
                throw new \RuntimeException(__('messages.groups_directory_publish_disabled'));
            }
            if (!$linkUrl) {
                throw new \RuntimeException(__('messages.directory_only_requires_link'));
            }
            if ($this->requestHasGallerySelection($request) || $postKind === 'repost') {
                throw new \RuntimeException(__('messages.directory_only_incompatible_post_type'));
            }

            DB::beginTransaction();
            try {
                $preview = $this->linkPreviewService->fetch($linkUrl);
                [$directory, $directoryStatus] = $this->createDirectoryEntry($request, $user, $preview, $text, $time);

                DB::commit();
                $this->securityThrottle->hitAction($user, 'post');
                return $directoryStatus;
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        if ($group && ($legacyDirectorySave || $publishMode === 'directory_only')) {
            throw new \RuntimeException(__('messages.groups_directory_publish_disabled'));
        }

        if ($postKind === 'repost' && !$this->schema->supports('reposts')) {
            throw new \RuntimeException($this->schema->blockedActionMessage('reposts', __('messages.reposts')));
        }

        if ($group && $postKind === 'repost') {
            throw new \RuntimeException(__('messages.groups_reposts_disabled'));
        }

        if ($hasMentions && !$this->schema->supports('mentions')) {
            throw new \RuntimeException($this->schema->blockedActionMessage('mentions', __('messages.mentions')));
        }

        DB::beginTransaction();
        try {
            $topic = $this->createForumTopic($user->id, $text, $postKind, $time, $group?->id);
            $status = Status::create([
                'uid' => $user->id,
                'group_id' => $group?->id,
                'date' => $time,
                's_type' => $statusType,
                'tp_id' => $topic->id,
                'statu' => 1,
            ]);

            if ($postKind === 'gallery') {
                $this->storeGalleryAssets($topic, $request, $user->id);
            }

            if (in_array($postKind, ['video', 'audio', 'file', 'music', 'clips'])) {
                $this->storeMediaAssets($topic, $request, $user->id, $postKind);
            }

            if ($linkUrl) {
                $preview = $this->linkPreviewService->fetch($linkUrl);
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

                if ($originalOwner && !$this->privacy->canRepost($originalOwner, $user) && (int) $originalOwner->id !== (int) $user->id && !$user->isAdmin()) {
                    throw new \RuntimeException(__('messages.reposts_disabled_for_user'));
                }

                StatusRepost::create([
                    'status_id' => $status->id,
                    'original_status_id' => $originalStatus->id,
                    'user_id' => $user->id,
                ]);

                if ($originalOwner) {
                    $this->notifications->send(
                        $originalOwner->id,
                        __('messages.repost_notification', ['user' => $user->username]),
                        '/t' . $topic->id,
                        'share',
                        $user->id,
                        'repost'
                    );
                }
            }

            $this->mentions->createStatusMentions($user, $status, $text, '/t' . $topic->id);

            $this->gamification->recordEvent($user->id, 'post_created');
            if ($postKind === 'repost') {
                $this->gamification->recordEvent($user->id, 'repost_created');
            } else {
                $this->gamification->refreshBadges($user->id);
            }

            DB::commit();
            $this->securityThrottle->hitAction($user, 'post');
            if ($group) {
                app(GroupMembershipService::class)->touchActivity($group, $time);
            }
            
            return $status;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Request $request, Status $status, User $user): Status
    {
        if ((int)$status->uid !== (int)$user->id && !$user->isAdmin()) {
            throw new \RuntimeException(__('messages.unauthorized'));
        }

        $settings = $this->getUploadSettings();
        $maxSizeKb = $settings['max_upload_size'] * 1024;
        $mimes = $settings['allowed_extensions'];
        $mimetypes = $settings['allowed_mime_types'];

        $rules = [
            'text' => 'nullable|string|max:10000',
            'txt' => 'nullable|string|max:10000',
            'images' => 'nullable|array|max:10',
            'images.*' => "image|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'videos' => 'nullable|array|max:1',
            'videos.*' => "file|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'audios' => 'nullable|array|max:1',
            'audios.*' => "file|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'files' => 'nullable|array|max:5',
            'files.*' => "file|mimes:{$mimes}|mimetypes:{$mimetypes}|max:{$maxSizeKb}",
            'delete_attachment_ids' => 'nullable|array',
            'delete_attachment_ids.*' => 'integer|exists:forum_attachments,id',
            'attachment_order' => 'nullable|array',
            'attachment_order.*' => 'integer|exists:forum_attachments,id',
        ];

        $request->validate($rules);
        
        $postKind = $this->determinePostKind($status);
        if (!$settings['video_sharing'] && $postKind === 'video' && $request->hasFile('videos')) {
            throw new \RuntimeException(__('messages.video_upload_disabled') ?? 'Video upload is disabled.');
        }
        if (!$settings['clips_upload'] && $postKind === 'clips' && $request->hasFile('videos')) {
            throw new \RuntimeException(__('messages.clips_upload_disabled') ?? 'Clips upload is disabled.');
        }
        if (!$settings['audio_sharing'] && in_array($postKind, ['audio', 'music']) && $request->hasFile('audios')) {
            throw new \RuntimeException(__('messages.audio_upload_disabled') ?? 'Audio upload is disabled.');
        }
        if (!$settings['file_sharing'] && $postKind === 'file' && $request->hasFile('files')) {
            throw new \RuntimeException(__('messages.file_upload_disabled') ?? 'File upload is disabled.');
        }

        $text = trim((string) $request->input('text', $request->input('txt', '')));

        if ($violation = $this->securityPolicy->textViolation($text, 'posts')) {
            throw new \RuntimeException($violation);
        }

        $hasMentions = \App\Support\ContentFormatter::extractMentionUsernames($text) !== [];
        if ($hasMentions && !$this->schema->supports('mentions')) {
            throw new \RuntimeException($this->schema->blockedActionMessage('mentions', __('messages.mentions')));
        }

        DB::beginTransaction();
        try {
            $topic = ForumTopic::where('id', $status->tp_id)->first();
            if ($topic) {
                $topic->update(['txt' => $text]);
            }
            // Directory only updates
            $directoryListing = Directory::where('id', $status->tp_id)->first();
            if ($directoryListing && $status->s_type == 1) {
                $directoryListing->update(['txt' => $text]);
            }
            
            // Delete requested attachments
            if ($request->filled('delete_attachment_ids')) {
                foreach ($request->input('delete_attachment_ids') as $attachmentId) {
                    $attachment = ForumAttachment::where('id', $attachmentId)->where('topic_id', $status->tp_id)->first();
                    if ($attachment) {
                        $this->deleteAttachmentFile($attachment);
                        $attachment->delete();
                    }
                }
            }

            // Store new files depending on post kind
            $postKind = $this->determinePostKind($status);
            if ($postKind === 'gallery' && $request->hasFile('images')) {
                $this->storeGalleryAssets($topic, $request, $user->id);
            } elseif (in_array($postKind, ['video', 'audio', 'file', 'music', 'clips'])) {
                // Ensure media asset arrays are present
                if ($request->hasFile('videos') || $request->hasFile('audios') || $request->hasFile('files')) {
                    $this->storeMediaAssets($topic, $request, $user->id, $postKind);
                }
            }

            // Sync gallery order
            if ($request->filled('attachment_order')) {
                foreach ($request->input('attachment_order') as $index => $attachmentId) {
                    ForumAttachment::where('id', $attachmentId)
                        ->where('topic_id', $status->tp_id)
                        ->update(['sort_order' => $index]);
                }
            }

            if ($topic) {
                $this->syncStatusType($topic->id);
            }

            // Refresh status logic
            $status->refresh();
            
            // Rewrite mentions
            if ($topic) {
                DB::table('status_mentions')->where('status_id', $status->id)->delete();
                $this->mentions->createStatusMentions($user, $status, $text, '/t' . $topic->id);
            }
            
            DB::commit();
            return $status;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(Status $status, User $user): void
    {
        if ((int)$status->uid !== (int)$user->id && !$user->isAdmin()) {
            throw new \RuntimeException(__('messages.unauthorized'));
        }

        DB::beginTransaction();
        try {
            // Delete related files
            if ($status->tp_id && !in_array($status->s_type, [1, 7867, 205, 6])) { // exclude non-forum types like store, directory if needed
                $attachments = ForumAttachment::where('topic_id', $status->tp_id)->get();
                foreach ($attachments as $attachment) {
                    $this->deleteAttachmentFile($attachment);
                    $attachment->delete();
                }
                
                ForumTopic::where('id', $status->tp_id)->delete();
                Option::where('o_parent', $status->tp_id)->where('o_type', 'image_post')->delete();
                \App\Models\ForumComment::where('post', $status->tp_id)->delete();
            }
            
            // Delete directory if s_type == 1
            if ($status->s_type == 1) {
                $directory = Directory::where('id', $status->tp_id)->first();
                if ($directory) {
                    Short::where('tp_id', $directory->id)->where('sh_type', 1)->delete();
                    $directory->delete();
                }
            }

            StatusLinkPreview::where('status_id', $status->id)->delete();
            StatusRepost::where('status_id', $status->id)->delete();
            DB::table('status_mentions')->where('status_id', $status->id)->delete();
            DB::table('like')->where('post', $status->id)->delete();
            
            $status->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createLegacyDirectoryPost(Request $request, User $user): Status
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'categ' => 'required|integer',
        ]);

        if ($cooldownMessage = $this->securityThrottle->actionMessage($user, 'post')) {
            throw new \RuntimeException($cooldownMessage);
        }

        if ($violation = $this->securityPolicy->textViolation((string) $request->input('txt', ''), 'posts')) {
            throw new \RuntimeException($violation);
        }

        if ($violation = $this->securityPolicy->urlViolation((string) $request->input('url'), 'posts')) {
            throw new \RuntimeException($violation);
        }

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

            $status = Status::create([
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
            $this->securityThrottle->hitAction($user, 'post');
            return $status;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
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

        $paths = [];
        if ($legacyPath && is_string($legacyPath)) {
            $paths[] = $legacyPath;
        }

        foreach ($uploadedFiles as $file) {
            $paths[] = $this->storeGalleryFile($file, $topic->id, $userId);
        }

        $paths = array_values(array_unique(array_filter($paths)));
        
        $currentCount = ForumAttachment::where('topic_id', $topic->id)->count();
        if (count($paths) + $currentCount > 10) {
            throw new \RuntimeException(__('messages.gallery_limit_exceeded'));
        }

        if ($paths === [] && $currentCount === 0) {
            throw new \RuntimeException(__('messages.gallery_requires_images'));
        }

        if (!empty($paths)) {
            Option::updateOrCreate(
                ['o_parent' => $topic->id, 'o_type' => 'image_post'],
                [
                    'name' => (string) time(),
                    'o_valuer' => $paths[0],
                    'o_order' => $userId,
                    'o_mode' => 'file',
                ]
            );
        }

        $maxSortOrder = ForumAttachment::where('topic_id', $topic->id)->max('sort_order') ?? 0;
        
        foreach ($paths as $index => $path) {
            ForumAttachment::create([
                'topic_id' => $topic->id,
                'user_id' => $userId,
                'file_path' => $path,
                'original_name' => basename($path),
                'mime_type' => $this->mimeTypeForPath($path),
                'file_size' => $this->fileSizeForPath($path),
                'sort_order' => $maxSortOrder + $index + 1,
            ]);
        }
    }

    private function storeMediaAssets(ForumTopic $topic, Request $request, int $userId, string $kind): void
    {
        $inputName = match ($kind) {
            'video', 'clips' => 'videos',
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

        $maxSortOrder = ForumAttachment::where('topic_id', $topic->id)->max('sort_order') ?? 0;

        foreach ($paths as $index => $path) {
            ForumAttachment::create([
                'topic_id' => $topic->id,
                'user_id' => $userId,
                'file_path' => $path,
                'original_name' => basename($path),
                'mime_type' => $this->mimeTypeForPath($path),
                'file_size' => $this->fileSizeForPath($path),
                'sort_order' => $maxSortOrder + $index + 1,
            ]);
        }
    }

    /**
     * Allowed file extensions for media uploads (security whitelist).
     */
    private const ALLOWED_MEDIA_EXTENSIONS = [
        // Images
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp',
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

        // SECURITY: Block dangerous file extensions
        if (!in_array($extension, self::ALLOWED_MEDIA_EXTENSIONS, true)) {
            throw new \RuntimeException(__('messages.file_type_not_allowed') ?? 'File type not allowed: ' . $extension);
        }

        // SECURITY: Block dangerous MIME types
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
        return is_file($file) ? (mime_content_type($file) ?: 'application/octet-stream') : 'application/octet-stream';
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
            if ($status->s_type == 4) {
                $status->update(['s_type' => 100]);
            }
            Option::where('o_parent', $topicId)->where('o_type', 'image_post')->delete();
        } else {
            // Check if gallery post
            $firstImage = $attachments->first(fn($a) => str_starts_with((string) ($a->mime_type ?? ''), 'image/'));
            if ($firstImage && $status->s_type == 100) {
                 $status->update(['s_type' => 4]);
            }
            
            $firstAttachment = $attachments->sortBy('sort_order')->first();
            if ($firstAttachment) {
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
    
    private function determinePostKind(Status $status): string
    {
        return match ((int)$status->s_type) {
            4 => 'gallery',
            10 => 'video',
            11 => 'audio',
            12 => 'file',
            13 => 'music',
            14 => 'clips',
            default => 'text',
        };
    }

    private function getUploadSettings(): array
    {
        $options = Option::where('o_type', 'file_upload_settings')->get()->keyBy('name');
        
        return [
            'file_sharing' => ($options['file_sharing']->o_valuer ?? '1') == '1',
            'video_sharing' => ($options['video_sharing']->o_valuer ?? '1') == '1',
            'clips_upload' => ($options['clips_upload']->o_valuer ?? '1') == '1',
            'audio_sharing' => ($options['audio_sharing']->o_valuer ?? '1') == '1',
            'max_upload_size' => (int) ($options['max_upload_size']->o_valuer ?? '10'),
            'allowed_extensions' => $options['allowed_extensions']->o_valuer ?? 'jpg,png,jpeg,gif,mp4,mp3,pdf,zip',
            'allowed_mime_types' => $options['allowed_mime_types']->o_valuer ?? 'image/jpeg,image/png,image/gif,video/mp4,audio/mpeg,application/pdf,application/zip',
        ];
    }
}
