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
        
        $query = ForumCategory::orderBy('sort_order', 'asc');
        
        if (!$user || !$user->isAdmin()) {
            $query->where(function($q) {
                $q->where('visibility', 'public')
                  ->orWhereNull('visibility')
                  ->orWhere('visibility', '');
            });
        }
        
        $categories = $query->get();

        return ForumCategoryResource::collection($categories);
    }

    public function topics($categoryId, Request $request)
    {
        $category = ForumCategory::findOrFail($categoryId);
        $user = Auth::guard('sanctum')->user();
        
        if ($category->visibility !== 'public' && $category->visibility !== null && $category->visibility !== '') {
            if (!$user || !$user->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $topics = ForumTopic::where('id_cat', $categoryId)
            ->with(['author'])
            ->orderBy('ep', 'desc')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return ForumTopicResource::collection($topics);
    }

    public function show($topicId, Request $request)
    {
        $topic = ForumTopic::with(['author'])->findOrFail($topicId);
        
        $category = ForumCategory::find($topic->id_cat);
        if ($category && $category->visibility !== 'public' && $category->visibility !== null && $category->visibility !== '') {
            $user = Auth::guard('sanctum')->user();
            if (!$user || !$user->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Increment views
        $topic->increment('vu');

        return new ForumTopicResource($topic);
    }
}
