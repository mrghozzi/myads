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
        // For general statuses, comments are usually in f_coment with tid = status->tp_id
        // This is a simplified fetch based on the most common status types (100, 2, 4)
        
        $comments = ForumComment::with('user')
            ->where('tid', $status->tp_id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    public function store(Request $request, Status $status)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $user = Auth::user();

        $comment = new ForumComment();
        $comment->uid = $user->id;
        $comment->tid = $status->tp_id; 
        $comment->txt = $request->input('text');
        $comment->date = time();
        $comment->save();

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => new CommentResource($comment)
        ], 201);
    }
}
