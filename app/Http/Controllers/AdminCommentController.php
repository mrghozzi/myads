<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use Illuminate\Http\Request;
use App\Services\PointLedgerService;
use Illuminate\Support\Facades\DB;

class AdminCommentController extends Controller
{
    public function index()
    {
        $comments = ForumComment::with(['user', 'topic'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin::admin.comments.index', compact('comments'));
    }

    public function destroy($id, PointLedgerService $pointLedger)
    {
        $comment = ForumComment::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete associated reactions/likes if any
            // ForumComment dbType in CommentController is 4
            $dbType = 4;
            $likes = \App\Models\Like::where('sid', $id)->where('type', $dbType)->get();
            foreach ($likes as $like) {
                \App\Models\Option::where('o_parent', $like->id)->where('o_type', 'data_reaction')->delete();
                $like->delete();
            }

            $uid = $comment->uid;
            $comment->delete();

            // Deduct points (optional, but consistent with CommentController@destroy)
            $pointLedger->award($uid, -2, 'comment_deleted_by_admin', 'comment_deleted', 'comment', $id);

            DB::commit();
            return redirect()->back()->with('success', __('messages.comment_deleted'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
