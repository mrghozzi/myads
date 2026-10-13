<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumComment;
use App\Models\Status;
use App\Http\Resources\ForumCategoryResource;
use App\Http\Resources\ForumTopicResource;
use App\Http\Resources\CommentResource;

class ForumApiController extends Controller
{
    public function categories(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        $query = ForumCategory::root()->with(['children'])->orderBy('ordercat', 'asc');
        
        if (!$user || !$user->isAdmin()) {
            $query->where(function($q) use ($user) {
                $q->where('visibility', '0')
                  ->orWhere('visibility', 0)
                  ->orWhereNull('visibility')
                  ->orWhere('visibility', '');
                if ($user) {
                    $q->orWhere('visibility', '1')
                      ->orWhere('visibility', 1);
                    if (method_exists($user, 'canModerateForum') && $user->canModerateForum()) {
                        $q->orWhere('visibility', '2')
                          ->orWhere('visibility', 2);
                    }
                }
            });
        }
        
        $categories = $query->get();

        foreach ($categories as $cat) {
            $catIds = array_merge([$cat->id], $cat->allChildIds());
            $cat->topics_count = ForumTopic::forumOnly()->whereIn('cat', $catIds)->where('statu', 1)->whereNull('group_id')->count();
            if ($cat->children) {
                foreach ($cat->children as $sub) {
                    $sub->topics_count = ForumTopic::forumOnly()->where('cat', $sub->id)->where('statu', 1)->whereNull('group_id')->count();
                }
            }
        }

        return ForumCategoryResource::collection($categories);
    }

    public function topics($categoryId, Request $request)
    {
        $category = ForumCategory::findOrFail($categoryId);
        $user = Auth::guard('sanctum')->user();
        
        if ($category->visibility == 1 && !$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if ($category->visibility == 2 && (!$user || !(method_exists($user, 'canModerateForum') && $user->canModerateForum()))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $catIds = array_merge([$category->id], $category->allChildIds());

        $query = ForumTopic::forumOnly()
            ->whereIn('cat', $catIds)
            ->where('statu', 1)
            ->whereNull('group_id')
            ->with(['user', 'category', 'lastPoster']);

        // Search query
        if ($request->filled('q')) {
            $q = trim((string) $request->query('q'));
            $query->where(function($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('txt', 'like', "%{$q}%");
            });
        }

        // Modern forum filtering & sorting
        $query->orderBy('is_pinned', 'desc');
        $filter = (string) $request->query('filter', 'latest');
        $query->filterModern($filter);

        $topics = $query->paginate(20);

        return ForumTopicResource::collection($topics);
    }

    public function show($topicId, Request $request)
    {
        $topic = ForumTopic::with(['user', 'category', 'lastPoster', 'attachments'])->findOrFail($topicId);
        
        $category = $topic->category;
        if ($category) {
            $user = Auth::guard('sanctum')->user();
            if ($category->visibility == 1 && !$user) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            if ($category->visibility == 2 && (!$user || !(method_exists($user, 'canModerateForum') && $user->canModerateForum()))) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Increment views
        $topic->increment('vu');

        // Eager load comments with nested replies and user profiles
        $comments = ForumComment::where('tid', $topicId)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('is_best_answer')
            ->orderBy('id', 'asc')
            ->get();

        $topic->setRelation('comments', $comments);

        return new ForumTopicResource($topic);
    }

    public function storeTopic(Request $request, $categoryId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $category = ForumCategory::findOrFail($categoryId);
        
        $user = Auth::guard('sanctum')->user();
        if ($category->visibility == 1 && !$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if ($category->visibility == 2 && (!$user || !(method_exists($user, 'canModerateForum') && $user->canModerateForum()))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $topic = ForumTopic::create([
            'cat' => $categoryId,
            'uid' => Auth::id(),
            'name' => $request->title,
            'txt' => $request->input('content'),
            'date' => time(),
            'statu' => 1,
            'is_pinned' => false,
            'last_posted_at' => time(),
            'last_poster_id' => Auth::id(),
        ]);

        Status::create([
            'uid' => Auth::id(),
            'date' => time(),
            's_type' => 2,
            'tp_id' => $topic->id,
        ]);

        app(\App\Services\GamificationService::class)->recordEvent(Auth::id(), 'forum_topic_created');

        return response()->json([
            'success' => true,
            'message' => __('messages.topic_created_successfully'),
            'data' => new ForumTopicResource($topic->load(['user', 'category']))
        ]);
    }

    public function storeReply(Request $request, $topicId)
    {
        $request->validate([
            'content' => 'required|string|min:2',
            'parent_id' => 'nullable|integer|exists:f_coment,id',
        ]);

        $topic = ForumTopic::findOrFail($topicId);

        if ($topic->is_locked) {
            $user = Auth::guard('sanctum')->user();
            $canModerate = $user && (
                $user->isAdmin() ||
                (method_exists($user, 'canModerateForum') && $user->canModerateForum('lock_topics', (int) $topic->cat))
            );
            if (!$canModerate) {
                return response()->json(['success' => false, 'message' => __('messages.topic_locked_for_comments')], 403);
            }
        }

        $parentId = $request->input('parent_id');
        if ($parentId) {
            $parentComment = ForumComment::find($parentId);
            if ($parentComment && $parentComment->parent_id) {
                $parentId = $parentComment->parent_id;
            }
        }

        $reply = ForumComment::create([
            'tid' => $topicId,
            'uid' => Auth::id(),
            'parent_id' => $parentId ?: null,
            'txt' => $request->input('content'),
            'date' => time(),
        ]);

        $topic->increment('reply');
        $topic->update([
            'last_posted_at' => time(),
            'last_poster_id' => Auth::id(),
            'comments_count' => ForumComment::where('tid', $topicId)->count(),
        ]);

        app(\App\Services\GamificationService::class)->recordEvent(Auth::id(), 'forum_reply_created');

        return response()->json([
            'success' => true,
            'message' => __('messages.reply_added_successfully'),
            'data' => new CommentResource($reply->load(['user', 'replies.user'])),
        ]);
    }

    public function togglePinTopic($topicId)
    {
        $user = Auth::guard('sanctum')->user();
        $topic = ForumTopic::findOrFail($topicId);

        $canPin = $user && ($user->isAdmin() || (method_exists($user, 'canModerateForum') && $user->canModerateForum('pin_topics', (int) $topic->cat)));
        if (!$canPin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $topic->is_pinned = !$topic->is_pinned;
        $topic->pinned_at = $topic->is_pinned ? time() : null;
        $topic->pinned_by = $topic->is_pinned ? $user->id : null;
        $topic->save();

        return response()->json(['success' => true, 'is_pinned' => (bool) $topic->is_pinned]);
    }

    public function toggleLockTopic($topicId)
    {
        $user = Auth::guard('sanctum')->user();
        $topic = ForumTopic::findOrFail($topicId);

        $canLock = $user && ($user->isAdmin() || (method_exists($user, 'canModerateForum') && $user->canModerateForum('lock_topics', (int) $topic->cat)));
        if (!$canLock) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $topic->is_locked = !$topic->is_locked;
        $topic->locked_at = $topic->is_locked ? time() : null;
        $topic->locked_by = $topic->is_locked ? $user->id : null;
        $topic->save();

        return response()->json(['success' => true, 'is_locked' => (bool) $topic->is_locked]);
    }

    public function togglePinComment($commentId)
    {
        $user = Auth::guard('sanctum')->user();
        $comment = ForumComment::findOrFail($commentId);
        $topic = ForumTopic::findOrFail($comment->tid);

        $isAuthor = $user && ((int) $user->id === (int) $topic->uid);
        $isModerator = $user && ($user->isAdmin() || (method_exists($user, 'canModerateForum') && $user->canModerateForum('pin_topics', (int) $topic->cat)));
        if (!$isAuthor && !$isModerator) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->is_pinned = !$comment->is_pinned;
        $comment->save();

        if ($comment->is_pinned) {
            $topic->update(['pinned_comment_id' => $comment->id]);
        } elseif ((int) $topic->pinned_comment_id === (int) $comment->id) {
            $topic->update(['pinned_comment_id' => null]);
        }

        return response()->json(['success' => true, 'is_pinned' => (bool) $comment->is_pinned]);
    }

    public function toggleBestAnswer($commentId)
    {
        $user = Auth::guard('sanctum')->user();
        $comment = ForumComment::findOrFail($commentId);
        $topic = ForumTopic::findOrFail($comment->tid);

        $isAuthor = $user && ((int) $user->id === (int) $topic->uid);
        $isModerator = $user && ($user->isAdmin() || (method_exists($user, 'canModerateForum') && $user->canModerateForum('edit_topics', (int) $topic->cat)));
        if (!$isAuthor && !$isModerator) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $newState = !$comment->is_best_answer;

        if ($newState) {
            ForumComment::where('tid', $topic->id)->where('is_best_answer', true)->update(['is_best_answer' => false]);
            $comment->is_best_answer = true;
            $comment->save();
            $topic->update(['best_comment_id' => $comment->id]);
        } else {
            $comment->is_best_answer = false;
            $comment->save();
            $topic->update(['best_comment_id' => null]);
        }

        return response()->json(['success' => true, 'is_best_answer' => (bool) $comment->is_best_answer]);
    }

    public function deleteComment($commentId)
    {
        $user = Auth::guard('sanctum')->user();
        $comment = ForumComment::findOrFail($commentId);
        $topic = ForumTopic::findOrFail($comment->tid);

        $isOwner = $user && ((int) $user->id === (int) $comment->uid);
        $isModerator = $user && ($user->isAdmin() || (method_exists($user, 'canModerateForum') && $user->canModerateForum('delete_comments', (int) $topic->cat)));
        if (!$isOwner && !$isModerator) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        ForumComment::where('parent_id', $comment->id)->delete();
        $comment->delete();

        $remainingCount = ForumComment::where('tid', $topic->id)->count();
        $topic->update([
            'reply' => $remainingCount,
            'comments_count' => $remainingCount,
        ]);

        return response()->json(['success' => true, 'message' => __('messages.comment_deleted_successfully')]);
    }
}
