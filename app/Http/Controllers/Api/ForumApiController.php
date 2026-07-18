<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Http\Resources\ForumCategoryResource;
use App\Http\Resources\ForumTopicResource;

class ForumApiController extends Controller
{
    public function categories(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        $query = ForumCategory::orderBy('ordercat', 'desc');
        
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

        $topics = ForumTopic::where('cat', $categoryId)
            ->with(['user'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return ForumTopicResource::collection($topics);
    }

    public function show($topicId, Request $request)
    {
        $topic = ForumTopic::with(['user'])->findOrFail($topicId);
        
        $category = ForumCategory::find($topic->cat);
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
            'id_cat' => $categoryId,
            'uid' => Auth::id(),
            'name' => $request->title,
            'txt' => $request->content,
            'date' => time(),
            'statu' => 1,
            'is_pinned' => false
        ]);

        app(\App\Services\GamificationService::class)->recordEvent(Auth::id(), 'forum_topic_created');

        return response()->json(['success' => true, 'message' => __('messages.topic_created_successfully'), 'data' => new ForumTopicResource($topic)]);
    }

    public function storeReply(Request $request, $topicId)
    {
        $request->validate([
            'content' => 'required|string|min:2',
        ]);

        $topic = ForumTopic::findOrFail($topicId);

        $reply = \App\Models\ForumComment::create([
            'tid' => $topicId,
            'uid' => Auth::id(),
            'txt' => $request->content,
            'date' => time(),
        ]);

        app(\App\Services\GamificationService::class)->recordEvent(Auth::id(), 'forum_reply_created');

        return response()->json(['success' => true, 'message' => __('messages.reply_added_successfully')]);
    }
}
