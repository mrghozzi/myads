<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReactionController extends Controller
{
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $uid = $user->id;
        
        $id = $request->input('id'); // Post/Topic/Site ID
        $type = $request->input('type'); // 'forum' or 'directory'
        $reaction = $request->input('reaction'); // like, love, etc.

        if (!$id || !$type || !$reaction) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Determine DB Type ID (2 for forum, 22 for directory, 3 for store)
        // Comment types: 4 (forum), 44 (directory), 444 (store)
        $dbType = 0;
        if ($type == 'forum') $dbType = 2;
        elseif ($type == 'directory') $dbType = 22;
        elseif ($type == 'store') $dbType = 3;
        elseif ($type == 'forum_comment') $dbType = 4;
        elseif ($type == 'directory_comment') $dbType = 44;
        elseif ($type == 'store_comment') $dbType = 444;

        if ($dbType === 0) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        // Find existing like
        $existingLike = Like::where('uid', $uid)
                            ->where('sid', $id)
                            ->where('type', $dbType)
                            ->first();

        // Determine size and icon based on type
        $isComment = in_array($dbType, [4, 44, 444]);
        $imgSize = $isComment ? 16 : 30;
        $defaultIcon = $isComment ? '<i class="fa fa-thumbs-up" aria-hidden="true"></i>' : '<svg class="post-option-icon icon-thumbs-up"><use xlink:href="#svg-thumbs-up"></use></svg>';

        DB::beginTransaction();
        try {
            if ($existingLike) {
                // Check existing option for this like
                $existingOption = Option::where('o_order', $uid)
                                      ->where('o_parent', $existingLike->id)
                                      ->where('o_type', 'data_reaction')
                                      ->first();

                if ($existingOption && $existingOption->o_valuer == $reaction) {
                    // Same reaction -> Toggle OFF (Remove)
                    $this->removeReaction($existingLike, $existingOption, $user, $id, $dbType);
                    $result = [
                        'action' => 'removed',
                        'html' => $defaultIcon
                    ];
                } else {
                    // Different reaction -> Update
                    if ($existingOption) {
                        $existingOption->update([
                            'name' => $reaction,
                            'o_valuer' => $reaction
                        ]);
                    } else {
                        // Should not happen if data is consistent, but handle it
                        Option::create([
                            'name' => $reaction,
                            'o_type' => 'data_reaction',
                            'o_order' => $uid,
                            'o_parent' => $existingLike->id,
                            'o_valuer' => $reaction,
                            'o_mode' => time()
                        ]);
                    }
                    $result = [
                        'action' => 'updated',
                        'html' => '<img class="reaction-option-image" src="' . asset('themes/default/assets/img/reaction/' . $reaction . '.png') . '" width="' . $imgSize . '" alt="reaction-' . $reaction . '">'
                    ];
                }
            } else {
                // New Reaction -> Toggle ON (Add)
                $this->addReaction($user, $id, $dbType, $reaction);
                $result = [
                    'action' => 'added',
                    'html' => '<img class="reaction-option-image" src="' . asset('themes/default/assets/img/reaction/' . $reaction . '.png') . '" width="' . $imgSize . '" alt="reaction-' . $reaction . '">'
                ];
            }

            DB::commit();
            return response()->json($result);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function removeReaction($like, $option, $user, $postId, $type)
    {
        $time_t = $like->time_t; // Original reaction time for notification matching
        
        // Delete Like
        $like->delete();
        
        // Delete Option
        if ($option) {
            $option->delete();
        }

        // Get Post Owner
        $ownerId = $this->getPostOwnerId($postId, $type);

        if ($ownerId && $ownerId != $user->id) {
            // Remove Notification
            // Legacy: DELETE FROM `notif` WHERE uid=:id AND time=:time AND state=1
            // Note: matching by exact time might be tricky if precision differs, but sticking to legacy logic
            Notification::where('uid', $ownerId)
                        ->where('time', $time_t)
                        ->where('state', 1)
                        ->delete();

            // Deduct Points
            // Owner -1
            User::where('id', $ownerId)->decrement('pts', 1);
            // Reactor -2
            User::where('id', $user->id)->decrement('pts', 2);
        }
    }

    private function addReaction($user, $postId, $type, $reaction)
    {
        $time = time();

        // Create Like
        $like = Like::create([
            'uid' => $user->id,
            'sid' => $postId,
            'type' => $type,
            'time_t' => $time,
        ]);

        // Create Option
        Option::create([
            'name' => $reaction,
            'o_type' => 'data_reaction',
            'o_order' => $user->id,
            'o_parent' => $like->id,
            'o_valuer' => $reaction,
            'o_mode' => $time
        ]);

        // Get Post Owner
        $ownerId = $this->getPostOwnerId($postId, $type);

        if ($ownerId && $ownerId != $user->id) {
            // Send Notification
            $postUrl = "";
            if ($type == 2) {
                $postUrl = "/t" . $postId;
            } elseif ($type == 22) {
                $postUrl = "/dr" . $postId;
            } elseif ($type == 3) {
                 // For store, we need product name.
                 $product = \App\Models\Product::find($postId);
                 if ($product) {
                     $postUrl = "/store/" . $product->name;
                 }
            } elseif ($type == 4) {
                // Forum Comment -> Link to Topic
                $comment = \App\Models\ForumComment::find($postId);
                if ($comment) {
                     $postUrl = "/t" . $comment->tid . "#comment_" . $postId; // Anchor to comment
                }
            } elseif ($type == 44) {
                // Directory Comment -> Link to Site
                $comment = \App\Models\Option::find($postId);
                if ($comment) {
                     $postUrl = "/dr" . $comment->o_parent . "#comment_" . $postId;
                }
            } elseif ($type == 444) {
                // Store Comment -> Link to Product
                $comment = \App\Models\Option::find($postId);
                if ($comment) {
                     $product = \App\Models\Product::find($comment->o_parent);
                     if ($product) {
                         $postUrl = "/store/" . $product->name . "#comment_" . $postId;
                     }
                }
            }

            $message = $user->username . " reacted to your post.";
            if (in_array($type, [4, 44, 444])) {
                $message = $user->username . " reacted to your comment.";
            }
            
            Notification::create([
                'uid' => $ownerId,
                'name' => $message,
                'nurl' => $postUrl,
                'logo' => $reaction, // Use reaction name (like, love...)
                'time' => $time,
                'state' => 1
            ]);

            // Add Points
            // Owner +1
            User::where('id', $ownerId)->increment('pts', 1);
            // Reactor +2
            User::where('id', $user->id)->increment('pts', 2);
        }
    }

    private function getPostOwnerId($postId, $type)
    {
        if ($type == 2) {
            // Forum Topic
            $topic = ForumTopic::find($postId);
            return $topic ? $topic->uid : null;
        } elseif ($type == 22) {
            // Directory Site
            $site = Directory::find($postId);
            return $site ? $site->uid : null;
        } elseif ($type == 3) {
            // Store Product
            $product = \App\Models\Product::find($postId);
            return $product ? $product->o_parent : null; // o_parent is user_id
        } elseif ($type == 4) {
            // Forum Comment
            $comment = \App\Models\ForumComment::find($postId);
            return $comment ? $comment->uid : null;
        } elseif ($type == 44) {
            // Directory Comment (Option)
            $comment = \App\Models\Option::find($postId);
            return $comment ? $comment->o_order : null; // o_order is uid for comments
        } elseif ($type == 444) {
            // Store Comment (Option)
            $comment = \App\Models\Option::find($postId);
            return $comment ? $comment->o_order : null; // o_order is uid for comments
        }
        return null;
    }
}
