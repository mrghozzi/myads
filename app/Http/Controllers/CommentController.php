<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Notification;
use App\Models\Option;
use App\Models\User;
use App\Services\GamificationService;
use App\Services\MentionService;
use App\Services\NotificationService;
use App\Services\PointLedgerService;
use App\Services\V420SchemaService;
use App\Support\ContentFormatter;
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
        $hide_form = false;
        $locked_topic = false;
        $forum_category_id = null;

        if (!$id || !$type) {
            return '';
        }

        $comments = [];
        if ($type == 'forum') {
            $topic = ForumTopic::find($id);
            if ($topic) {
                $forum_category_id = (int) $topic->cat;
                $locked_topic = (bool) $topic->is_locked;
                $hide_form = $locked_topic
                    && (!Auth::check() || !$this->canUserCommentLockedTopic(Auth::user(), $topic));
            }

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
        } elseif ($type == 'order') {
            $order = \App\Models\OrderRequest::find($id);
            if ($order && $order->statu == 0) {
                $hide_form = true;
            }
            $comments = Option::where('o_parent', $id)
                ->where('o_type', 'order_comment')
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();
        }

        // We need to reverse the order for display (oldest first? No, usually newest at bottom, but let's check old code. 
        // Old code: ORDER BY `id` DESC. But it displays them. Usually chat/comments are DESC but maybe UI reverses?
        // Old code: while($sutcat=$catsum->fetch...) -> loops through results.
        // If query is DESC, first result is newest.
        // Let's keep it DESC for now.

        return view('theme::partials.activity.comments', compact('comments', 'id', 'type', 'limit', 'hide_form', 'locked_topic', 'forum_category_id'));
    }

    public function store(
        Request $request,
        NotificationService $notifications,
        PointLedgerService $pointLedger,
        MentionService $mentions,
        GamificationService $gamification
    )
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
        $topic = null;

        if (!in_array($type, ['forum', 'directory', 'store', 'order'], true)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $schema = app(V420SchemaService::class);
        if (ContentFormatter::extractMentionUsernames($text) !== [] && !$schema->supports('mentions')) {
            return response()->json([
                'error' => $schema->blockedActionMessage('mentions', __('messages.mentions')),
            ], 409);
        }

        if ($type === 'forum') {
            $topic = ForumTopic::find($id);
            if (!$topic) {
                return response()->json(['error' => 'Topic not found'], 404);
            }

            if ($topic->is_locked && !$this->canUserCommentLockedTopic($user, $topic)) {
                return response()->json(['error' => __('messages.topic_locked_for_comments')], 403);
            }
        }

        DB::beginTransaction();
        try {
            if ($type == 'forum') {
                $comment = ForumComment::create([
                    'uid' => $uid,
                    'tid' => $id,
                    'txt' => $text,
                    'date' => $time
                ]);

                $ownerId = $topic->uid;
                $url = "/t" . $id; // Legacy URL format support (Root relative)

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
            } elseif ($type == 'order') {
                $order = \App\Models\OrderRequest::findOrFail($id);
                if ($order->statu == 0) {
                    return response()->json(['error' => __('messages.order_closed_for_comments')], 403);
                }
                $comment = new \App\Models\Option();
                $comment->name = 'coment_order'; // Fixed name for order comments
                $comment->o_valuer = $text; // Comment text
                $comment->o_type = 'order_comment';
                $comment->o_parent = (int) $id;
                $comment->o_order = (int) Auth::id();
                $comment->o_mode = 0; // Default rating
                $comment->save();

                // Update last activity
                $order->update(['last_activity' => time()]);

                // PTS Reward for commenter
                app(\App\Services\PointLedgerService::class)->award(Auth::user(), 5, 'order_reply', 'points_awarded', 'order_comment', $comment->id);
                
                // Reward owner for getting a reply (only if not replying to self)
                if ($order->uid != Auth::id()) {
                    app(\App\Services\PointLedgerService::class)->award($order->uid, 2, 'order_received_reply', 'points_awarded', 'order', $order->id);
                }

                $ownerId = $order->uid;
                $url = "/orders/" . $order->id;
                
                app(\App\Services\GamificationService::class)->recordEvent($uid, 'order_offer_created');
            }

            $mentions->createCommentMentions($user, $type, (int) $comment->id, $text, $url);
            $gamification->recordEvent($uid, 'comment_created');
            if ($type === 'forum') {
                $gamification->recordEvent($uid, 'forum_reply_created');
            }

            if ($ownerId && $ownerId != $uid) {
                $notifications->send(
                    $ownerId,
                    __('messages.comment_notification', ['user' => $user->username]),
                    $url,
                    $logo,
                    $uid
                );

                $pointLedger->award($ownerId, 1, 'comment_received', 'comment_received', 'comment', (int) $comment->id);
                $pointLedger->award($uid, 2, 'comment_created', 'comment_created', 'comment', (int) $comment->id);
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
            \Log::error('Comment Store Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, PointLedgerService $pointLedger)
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
        } elseif ($type == 'order') {
            $comment = Option::where('id', $id)->where('o_type', 'order_comment')->first();
            $dbType = 66;
        }

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $ownerId = ($type == 'forum') ? $comment->uid : $comment->o_order;
        $isOwner = ($ownerId == $uid);

        $canDeleteAsForumModerator = false;
        if ($type === 'forum' && $comment instanceof ForumComment) {
            $topic = ForumTopic::find($comment->tid);
            if ($topic) {
                $canDeleteAsForumModerator = Auth::user()->canModerateForum('delete_comments', (int) $topic->cat);
            }
        }

        if (!($isOwner || Auth::user()->isAdmin() || $canDeleteAsForumModerator)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            if ($dbType > 0) {
                $likes = \App\Models\Like::where('sid', $id)->where('type', $dbType)->get();
                foreach ($likes as $like) {
                    \App\Models\Option::where('o_parent', $like->id)->where('o_type', 'data_reaction')->delete();
                    $like->delete();
                }
            }

            $comment->delete();

            if ($isOwner) {
                $pointLedger->award($uid, -2, 'comment_deleted', 'comment_deleted', 'comment', (int) $comment->id);
            }

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function canUserCommentLockedTopic(User $user, ForumTopic $topic): bool
    {
        return (int) $user->id === (int) $topic->uid
            || $user->canModerateForum('lock_topics', (int) $topic->cat);
    }
}
