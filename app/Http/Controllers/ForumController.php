<?php

namespace App\Http\Controllers;

use App\Models\Emoji;
use App\Models\ForumAttachment;
use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Option;
use App\Models\Status;
use App\Services\GamificationService;
use App\Support\ForumSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::where(function($query) {
                $user = auth()->user();
                $query->where('visibility', 0); // Everyone
                if ($user) {
                    $query->orWhere('visibility', 1); // Members
                    if ($user->canModerateForum()) {
                        $query->orWhere('visibility', 2); // Mods
                    }
                }
            })
            ->orderBy('ordercat', 'desc')
            ->get();

        $this->seo([
            'scope_key' => 'forum_index',
            'resource_title' => __('messages.seo_forum_title'),
            'description' => __('messages.seo_forum_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.forum'), 'url' => route('forum.index')],
            ],
        ]);

        return view('theme::forum.index', compact('categories'));
    }

    public function category($id)
    {
        $category = ForumCategory::findOrFail($id);

        $user = auth()->user();
        if ($category->visibility == 1 && !$user) {
            abort(403);
        }
        if ($category->visibility == 2 && (!$user || !$user->canModerateForum())) {
            abort(403);
        }

        $settings = ForumSettings::all();
        $time = time();
        $sidebarCategories = $this->buildCategorySidebar((int) $category->id);

        $statuses = Status::visible()
            ->whereIn('s_type', [2, 4])
            ->where('date', '<=', $time)
            ->whereIn('tp_id', function ($query) use ($id) {
                $query->select('id')
                    ->from('forum')
                    ->where('cat', $id)
                    ->where('statu', 1);
            });

        if (Schema::hasColumn('forum', 'is_pinned')) {
            $statuses->orderByRaw('(SELECT is_pinned FROM forum WHERE forum.id = status.tp_id) DESC');
        }

        $statuses = $statuses
            ->orderBy('id', 'desc')
            ->paginate(20);

        $topicIds = $statuses->pluck('tp_id');
        $topics = ForumTopic::with(['user', 'category'])
            ->whereIn('id', $topicIds)
            ->get()
            ->keyBy('id');

        $this->seo([
            'scope_key' => 'forum_category',
            'content_type' => 'forum_category',
            'content_id' => $category->id,
            'resource_title' => $category->name,
            'category_name' => $category->name,
            'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $category->txt))), 170, '') ?: __('messages.seo_forum_category_description', ['category' => $category->name]),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.forum'), 'url' => route('forum.index')],
                ['name' => $category->name, 'url' => route('forum.category', $category->id)],
            ],
        ]);

        return view('theme::forum.category', compact('category', 'statuses', 'topics', 'sidebarCategories'));
    }

    public function topic($id)
    {
        $topic = ForumTopic::visible()->with(['user', 'category', 'comments.user', 'attachments'])->findOrFail($id);
        
        $category = $topic->category;
        if ($category) {
            $user = auth()->user();
            if ($category->visibility == 1 && !$user) {
                abort(403);
            }
            if ($category->visibility == 2 && (!$user || !$user->canModerateForum())) {
                abort(403);
            }
        }

        $forumSettings = ForumSettings::all();

        $status = Status::where('tp_id', $id)
            ->whereIn('s_type', [2, 4, 100, 7867])
            ->firstOrFail();

        if ($status->s_type == 7867) {
            $product = \App\Models\Product::withoutGlobalScope('store')->find($id);
            if ($product) {
                return redirect()->route('store.show', $product->name);
            }
        }

        $seoContext = [
            'scope_key' => 'forum_topic',
            'content_type' => 'forum_topic',
            'content_id' => $topic->id,
            'resource_title' => $topic->name,
            'category_name' => $topic->category?->name,
            'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $topic->txt))), 170, ''),
            'image' => $topic->image_url,
            'lastmod' => $topic->date ?: $status->date,
            'schema_type' => 'DiscussionForumPosting',
            'author_name' => $topic->user?->username,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.forum'), 'url' => route('forum.index')],
                ['name' => $topic->category?->name ?: __('messages.category_fallback'), 'url' => route('forum.category', $topic->cat)],
                ['name' => $topic->name, 'url' => route('forum.topic', $topic->id)],
            ],
        ];

        if ($status->s_type == 100) {
            $this->seo($seoContext);
            return view('theme::forum.post', compact('topic', 'status', 'forumSettings'));
        }

        if ($status->s_type == 4) {
            $this->seo($seoContext);
            return view('theme::forum.image', compact('topic', 'status', 'forumSettings'));
        }

        $this->seo($seoContext);

        return view('theme::forum.topic', compact('topic', 'status', 'forumSettings'));
    }

    public function create(Request $request)
    {
        if (!auth()->check()) {
            abort(404);
        }

        $categories = ForumCategory::orderBy('ordercat', 'asc')->get();
        $emojis = Emoji::orderBy('id', 'asc')->get();
        $forumSettings = ForumSettings::all();
        $topic = null;
        $editType = null;

        if ($request->filled('e')) {
            $topic = ForumTopic::with('attachments')->findOrFail($request->input('e'));

            if (!$this->canEditTopic($topic, auth()->user())) {
                abort(403);
            }

            $status = Status::where('tp_id', $topic->id)->whereIn('s_type', [2, 7867])->first();
            $editType = $status?->s_type ?? 2;
        }

        return view('theme::forum.create', compact('categories', 'emojis', 'topic', 'editType', 'forumSettings'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('cat') && $request->filled('categ')) {
            $request->merge(['cat' => $request->input('categ')]);
        }

        if (!$request->has('type')) {
            $request->merge(['type' => 100]);
        }

        $settings = ForumSettings::all();

        $rules = [
            'name' => 'required|string|max:255',
            'txt' => 'required|string',
            'cat' => 'required|integer|exists:f_cat,id',
            'type' => 'required|in:100,4',
            'img' => 'nullable|image|max:2048',
        ];

        $rules = array_merge($rules, $this->attachmentValidationRules($settings));
        $request->validate($rules);

        $uid = auth()->id();
        if (!$uid) {
            abort(403);
        }

        $time = time();

        DB::beginTransaction();
        try {
            $imagePath = null;
            if ((int) $request->type === 4 && $request->hasFile('img')) {
                $file = $request->file('img');
                $filename = time() . '_' . Str::random(12) . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload'), $filename);
                $imagePath = 'upload/' . $filename;
            }

            $topicData = [
                'uid' => $uid,
                'name' => $request->name,
                'txt' => $request->txt,
                'cat' => $request->cat,
                'statu' => 1,
            ];

            if (Schema::hasColumn('forum', 'date')) {
                $topicData['date'] = $time;
            }
            if (Schema::hasColumn('forum', 'reply')) {
                $topicData['reply'] = 0;
            }
            if (Schema::hasColumn('forum', 'vu')) {
                $topicData['vu'] = 0;
            }

            $topic = ForumTopic::create($topicData);

            $status = Status::create([
                'uid' => $uid,
                'date' => $time,
                's_type' => (int) $request->type === 4 ? 4 : 2,
                'tp_id' => $topic->id,
            ]);

            if ($imagePath) {
                Option::create([
                    'name' => (string) $time,
                    'o_valuer' => $imagePath,
                    'o_type' => 'image_post',
                    'o_parent' => $topic->id,
                    'o_order' => $uid,
                    'o_mode' => 'file',
                ]);

                $status->update(['s_type' => 4]);
            }

            $this->storeTopicAttachments($topic, $request, $settings);

            GamificationService::recordEvent('forum_topic_created', auth()->user());

            DB::commit();
            return redirect()->route('forum.topic', $topic->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['forum' => $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $topic = ForumTopic::with('attachments')->findOrFail($id);

        if (!$this->canEditTopic($topic, auth()->user())) {
            abort(403);
        }

        $categories = ForumCategory::orderBy('ordercat', 'asc')->get();
        $forumSettings = ForumSettings::all();
        $status = Status::where('tp_id', $id)->whereIn('s_type', [2, 4, 100])->first();
        return view('theme::forum.edit', compact('topic', 'categories', 'forumSettings', 'status'));
    }

    public function update(Request $request, $id)
    {
        if (!$request->filled('cat') && $request->filled('categ')) {
            $request->merge(['cat' => $request->input('categ')]);
        }

        $topic = ForumTopic::with('attachments')->findOrFail($id);

        if (!$request->filled('cat')) {
            $request->merge(['cat' => (int) $topic->cat]);
        }

        if (!$this->canEditTopic($topic, auth()->user())) {
            abort(403);
        }

        $settings = ForumSettings::all();

        $rules = [
            'name' => 'required|string|max:255',
            'txt' => 'required|string',
            'cat' => ((int) $topic->cat === 0) ? 'required|integer' : 'required|integer|exists:f_cat,id',
            'delete_attachments' => 'nullable|array',
            'delete_attachments.*' => 'integer',
        ];

        $rules = array_merge($rules, $this->attachmentValidationRules($settings));
        $request->validate($rules);

        DB::beginTransaction();
        try {
            $topic->update([
                'name' => $request->name,
                'txt' => $request->txt,
                'cat' => $request->cat,
            ]);

            $deleteAttachmentIds = collect($request->input('delete_attachments', []))
                ->filter(fn ($id) => is_numeric($id))
                ->map(fn ($id) => (int) $id)
                ->values();

            if ($deleteAttachmentIds->isNotEmpty()) {
                $attachmentsToDelete = $topic->attachments()->whereIn('id', $deleteAttachmentIds)->get();
                $this->deleteTopicAttachments($attachmentsToDelete);
            }

            $this->storeTopicAttachments($topic, $request, $settings);

            DB::commit();
            return redirect()->route('forum.topic', $topic->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['forum' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Missing ID'], 400);
        }

        $topic = ForumTopic::with('attachments')->find($id);
        if (!$topic) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if (!$this->canDeleteTopic($topic, auth()->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            Status::where('tp_id', $id)->whereIn('s_type', [2, 4, 100, 7867])->delete();

            Option::where('o_parent', $id)
                ->whereIn('o_type', ['image_post', 'data_reaction'])
                ->delete();

            ForumComment::where('tid', $id)->delete();

            $this->deleteTopicAttachments($topic->attachments);

            $topic->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('forum.index');
    }

    public function togglePin(Request $request, $topicId)
    {
        $topic = ForumTopic::findOrFail($topicId);
        $user = auth()->user();

        if (!$user || !$user->canModerateForum('pin_topics', (int) $topic->cat)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => __('messages.forum_unauthorized')], 403);
            }

            abort(403);
        }

        $topic->is_pinned = !$topic->is_pinned;
        if ($topic->is_pinned) {
            $topic->pinned_at = time();
            $topic->pinned_by = $user->id;
        } else {
            $topic->pinned_at = null;
            $topic->pinned_by = null;
        }
        $topic->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_pinned' => (bool) $topic->is_pinned,
                'message' => $topic->is_pinned ? __('messages.topic_pinned') : __('messages.topic_unpinned'),
            ]);
        }

        return back()->with('success', $topic->is_pinned ? __('messages.topic_pinned') : __('messages.topic_unpinned'));
    }

    public function toggleLock(Request $request, $topicId)
    {
        $topic = ForumTopic::findOrFail($topicId);
        $user = auth()->user();

        if (!$user || !$user->canModerateForum('lock_topics', (int) $topic->cat)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => __('messages.forum_unauthorized')], 403);
            }

            abort(403);
        }

        $topic->is_locked = !$topic->is_locked;
        if ($topic->is_locked) {
            $topic->locked_at = time();
            $topic->locked_by = $user->id;
        } else {
            $topic->locked_at = null;
            $topic->locked_by = null;
        }
        $topic->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_locked' => (bool) $topic->is_locked,
                'message' => $topic->is_locked ? __('messages.topic_locked') : __('messages.topic_unlocked'),
            ]);
        }

        return back()->with('success', $topic->is_locked ? __('messages.topic_locked') : __('messages.topic_unlocked'));
    }

    public function downloadAttachment(Request $request, $attachmentId)
    {
        $attachment = ForumAttachment::findOrFail($attachmentId);

        $relativePath = ltrim((string) $attachment->file_path, '/\\');
        if ($relativePath === '') {
            abort(404);
        }

        $normalizedPath = str_replace(['..', '\\'], ['', '/'], $relativePath);
        $storageFile = storage_path('app/' . $normalizedPath);
        $legacyPublicFile = public_path($normalizedPath);

        if (is_file($storageFile)) {
            $filePath = $storageFile;
        } elseif (is_file($legacyPublicFile)) {
            $filePath = $legacyPublicFile;
        } elseif (is_file(base_path($normalizedPath))) {
            $filePath = base_path($normalizedPath);
        } else {
            abort(404);
        }

        $downloadName = trim((string) $attachment->original_name);
        if ($downloadName === '') {
            $downloadName = basename($filePath);
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $wantsInline = $request->boolean('inline');
        if ($wantsInline && str_starts_with($mimeType, 'image/')) {
            return response()->file($filePath, ['Content-Type' => $mimeType]);
        }

        return response()->download($filePath, $downloadName);
    }

    private function buildCategorySidebar(int $currentCategoryId)
    {
        $topicCounts = ForumTopic::visible()
            ->select('cat', DB::raw('COUNT(*) as topic_count'))
            ->where('statu', 1)
            ->groupBy('cat')
            ->pluck('topic_count', 'cat');

        return ForumCategory::query()
            ->where(function($query) {
                $user = auth()->user();
                $query->where('visibility', 0);
                if ($user) {
                    $query->orWhere('visibility', 1);
                    if ($user->canModerateForum()) {
                        $query->orWhere('visibility', 2);
                    }
                }
            })
            ->orderBy('ordercat', 'desc')
            ->get()
            ->map(function (ForumCategory $boardCategory) use ($topicCounts, $currentCategoryId) {
                $description = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $boardCategory->txt)));

                return [
                    'category' => $boardCategory,
                    'topic_count' => (int) ($topicCounts[$boardCategory->id] ?? 0),
                    'description' => Str::limit($description, 80),
                    'is_active' => (int) $boardCategory->id === $currentCategoryId,
                ];
            });
    }

    private function attachmentValidationRules(array $settings): array
    {
        if ((int) $settings['attachments_enabled'] !== 1) {
            return [];
        }

        $allowed = ForumSettings::allowedExtensions();
        $mimes = implode(',', $allowed);

        return [
            'attachments' => 'nullable|array|max:' . (int) $settings['max_attachments_per_topic'],
            'attachments.*' => 'file|max:' . (int) $settings['max_attachment_size_kb'] . '|mimes:' . $mimes,
        ];
    }

    private function storeTopicAttachments(ForumTopic $topic, Request $request, array $settings): void
    {
        if ((int) $settings['attachments_enabled'] !== 1 || !$request->hasFile('attachments')) {
            return;
        }

        $files = $request->file('attachments', []);
        if (empty($files)) {
            return;
        }

        $currentCount = (int) $topic->attachments()->count();
        $maxAllowed = (int) $settings['max_attachments_per_topic'];
        if (($currentCount + count($files)) > $maxAllowed) {
            throw new \RuntimeException(__('messages.attachments_limit_exceeded'));
        }

        $destinationPath = base_path('upload');
        if (!is_dir($destinationPath) && !mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
            throw new \RuntimeException('Unable to create forum attachment directory.');
        }

        $nextSortOrder = (int) ($topic->attachments()->max('sort_order') ?? 0);

        foreach ($files as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
            $filename = 'topic_' . $topic->id . '_' . time() . '_' . Str::random(12) . '.' . $extension;
            $originalName = $file->getClientOriginalName();
            $mimeType = (string) ($file->getClientMimeType() ?: '');
            $fileSize = (int) $file->getSize();

            $movedFile = $file->move($destinationPath, $filename);

            // Uploaded tmp file is removed after move; fallback to destination metadata.
            if ($fileSize <= 0 && is_file($movedFile->getPathname())) {
                $fileSize = (int) filesize($movedFile->getPathname());
            }
            if ($mimeType === '') {
                $mimeType = (string) ($movedFile->getMimeType() ?: 'application/octet-stream');
            }

            ForumAttachment::create([
                'topic_id' => $topic->id,
                'user_id' => (int) auth()->id(),
                'file_path' => 'upload/' . $filename,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'sort_order' => $nextSortOrder + $index + 1,
            ]);
        }
    }

    private function deleteTopicAttachments($attachments): void
    {
        foreach ($attachments as $attachment) {
            $relativePath = ltrim((string) $attachment->file_path, '/\\');
            $normalizedPath = str_replace(['..', '\\'], ['', '/'], $relativePath);

            $storageFile = storage_path('app/' . $normalizedPath);
            $legacyPublicFile = public_path($normalizedPath);

            if (is_file($storageFile)) {
                @unlink($storageFile);
            } elseif (is_file($legacyPublicFile)) {
                @unlink($legacyPublicFile);
            } elseif (is_file(base_path($normalizedPath))) {
                @unlink(base_path($normalizedPath));
            }

            $attachment->delete();
        }
    }

    private function canEditTopic(ForumTopic $topic, $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->canModerateForum('edit_topics', (int) $topic->cat)) {
            return true;
        }

        if ((int) $topic->uid !== (int) $user->id) {
            return false;
        }

        return !$topic->is_locked;
    }

    private function canDeleteTopic(ForumTopic $topic, $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->canModerateForum('delete_topics', (int) $topic->cat)) {
            return true;
        }

        if ((int) $topic->uid !== (int) $user->id) {
            return false;
        }

        return !$topic->is_locked;
    }
}
