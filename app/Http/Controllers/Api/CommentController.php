<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;
use App\Models\ForumComment;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    public function index(Status $status)
    {
        $targetTid = (int) ($status->tp_id ?: $status->id);
        $comments = ForumComment::with(['user', 'replies.user'])
            ->where(function ($q) use ($status, $targetTid) {
                if ($status->tp_id) {
                    $q->where('tid', $status->tp_id);
                } else {
                    $q->where('tid', $targetTid);
                }
            })
            ->whereNull('parent_id')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    public function store(Request $request, Status $status)
    {
        $request->validate([
            'text' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);

        $text = trim((string) $request->input('text'));
        if (app(\App\Services\ModerationService::class)->containsProfanity($text)) {
            return response()->json([
                'message' => __('messages.moderation_profanity_blocked') ?? 'Your comment contains prohibited words.',
            ], 422);
        }

        $user = Auth::user();
        $targetTid = (int) ($status->tp_id ?: $status->id);
        $parentId = $request->filled('parent_id') ? (int) $request->input('parent_id') : null;
        $parentComment = null;
        if ($parentId) {
            $parentComment = ForumComment::where('id', $parentId)->first();
            if (!$parentComment) {
                $parentId = null;
            }
        }

        $comment = new ForumComment();
        $comment->uid = $user->id;
        $comment->tid = $targetTid; 
        $comment->parent_id = $parentId;
        $comment->txt = $text;
        $comment->date = time();
        $comment->save();

        if ($parentComment && (int) $parentComment->uid !== (int) $user->id) {
            $parentAuthor = \App\Models\User::find($parentComment->uid);
            if ($parentAuthor) {
                app(\App\Services\NotificationService::class)->send(
                    $parentAuthor,
                    __('messages.user_replied_to_your_comment', ['user' => $user->username]),
                    '',
                    'reply',
                    $user->id,
                    'forum_reply'
                );
            }
        }

        $resource = new CommentResource($comment->load('user'));

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $resource,
            'comment' => $resource,
        ], 201);
    }
}
