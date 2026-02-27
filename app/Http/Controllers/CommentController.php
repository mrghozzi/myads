<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Notification;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function load(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type'); // 'forum' or 'directory'
        $limit = $request->input('limit', 5);

        if (!$id || !$type) {
            return '';
        }

        $comments = [];
        if ($type == 'forum') {
            $comments = ForumComment::where('tid', $id)
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
        } elseif ($type == 'directory') {
            $comments = Option::where('o_parent', $id)
                ->where('o_type', 'd_coment')
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
        } elseif ($type == 'store') {
            $comments = Option::where('o_parent', $id)
                ->where('o_type', 's_coment')
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
        }

        // We need to reverse the order for display (oldest first? No, usually newest at bottom, but let's check old code. 
        // Old code: ORDER BY `id` DESC. But it displays them. Usually chat/comments are DESC but maybe UI reverses?
        // Old code: while($sutcat=$catsum->fetch...) -> loops through results.
        // If query is DESC, first result is newest.
        // Let's keep it DESC for now.

        return view('theme::partials.activity.comments', compact('comments', 'id', 'type', 'limit'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $uid = $user->id;
        $id = $request->input('id');
        $type = $request->input('type');
        $text = $request->input('comment');

        if (!$id || !$type || !$text) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $time = time();
        $ownerId = 0;
        $url = '';
        $logo = 'comment';

        DB::beginTransaction();
        try {
            if ($type == 'forum') {
                $comment = ForumComment::create([
                    'uid' => $uid,
                    'tid' => $id,
                    'txt' => $text,
                    'date' => $time
                ]);
                
                $topic = ForumTopic::find($id);
                if ($topic) {
                    $ownerId = $topic->uid;
                    $url = "/t" . $id; // Legacy URL format support (Root relative)
                }

            } elseif ($type == 'directory') {
                $comment = Option::create([
                    'name' => 'coment_dir',
                    'o_type' => 'd_coment',
                    'o_order' => $uid,
                    'o_parent' => $id,
                    'o_valuer' => $text,
                    'o_mode' => $time
                ]);

                $site = Directory::find($id);
                if ($site) {
                    $ownerId = $site->uid;
                    $url = "/dr" . $id; // Legacy URL format support (Root relative)
                }
            } elseif ($type == 'store') {
                $comment = Option::create([
                    'name' => 'coment_store',
                    'o_type' => 's_coment',
                    'o_order' => $uid,
                    'o_parent' => $id,
                    'o_valuer' => $text,
                    'o_mode' => $time
                ]);

                // Product is stored in options table, but we use Product model
                $product = \App\Models\Product::find($id);
                if ($product) {
                    $ownerId = $product->o_parent; // o_parent is user_id for Product
                    $url = "/store/" . $product->name; // URL format (Root relative)
                }
            }

            // Notifications and Points
            if ($ownerId && $ownerId != $uid) {
                $message = $user->username . " commented on your posts";
                Notification::create([
                    'uid' => $ownerId,
                    'name' => $message,
                    'nurl' => $url,
                    'logo' => $logo,
                    'time' => $time,
                    'state' => 1
                ]);

                // Points: Owner +1, Commenter +2
                User::where('id', $ownerId)->increment('pts', 1);
                User::where('id', $uid)->increment('pts', 2);
            }

            DB::commit();
            
            // Return the single comment view for appending
            // Or reload the list. Old code returns the list/form logic or just appends?
            // post_comment.php handles the list. 
            // The AJAX success in old code: $(".comment_form...").html(result);
            // It seems it might return the comment HTML?
            // Wait, old code: success : function(result) { $(".comment_form...").html(result); }
            // And post_comment.php calls itself recursively to reload? 
            // Actually, let's just return the new comment HTML or the full list.
            // For simplicity and matching old behavior (reloading list often), let's return the load view again?
            // No, better to just return the new comment HTML to append, OR reload the whole comment section via JS.
            // Let's stick to reloading the comments section.
            
            return $this->load($request);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $id = $request->input('trashid');
        $type = $request->input('type'); // 'forum' or 'directory'
        $uid = Auth::id();

        if (!$id || !$type) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        DB::beginTransaction();
        try {
            $comment = null;
            $dbType = 0;

            if ($type == 'forum') {
                $comment = ForumComment::find($id);
                $dbType = 4;
            } elseif ($type == 'directory') {
                $comment = Option::where('id', $id)->where('o_type', 'd_coment')->first();
                $dbType = 44;
            } elseif ($type == 'store') {
                $comment = Option::where('id', $id)->where('o_type', 's_coment')->first();
                $dbType = 444;
            }

            if (!$comment) {
                return response()->json(['error' => 'Comment not found'], 404);
            }

            // Check permission: Owner or Admin
            // For ForumComment: uid is the owner
            // For Option (Directory/Store): o_order is the owner (uid)
            $ownerId = ($type == 'forum') ? $comment->uid : $comment->o_order;
            $isOwner = ($ownerId == $uid);
            
            if ($isOwner || Auth::user()->isAdmin()) {
                // Delete Reactions associated with this comment
                if ($dbType > 0) {
                     $likes = \App\Models\Like::where('sid', $id)->where('type', $dbType)->get();
                     foreach ($likes as $like) {
                         \App\Models\Option::where('o_parent', $like->id)->where('o_type', 'data_reaction')->delete();
                         $like->delete();
                     }
                }

                $comment->delete();

                // Deduct points only if owner deleted it (to discourage spam/delete cycles? Or just reverse the point gain)
                // Original code deducted points if owner deleted.
                if ($isOwner) {
                    User::where('id', $uid)->decrement('pts', 2);
                }
            } else {
                 return response()->json(['error' => 'Unauthorized'], 403);
            }

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
