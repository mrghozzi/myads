<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Option;
use App\Models\Status;
use App\Models\User;
use App\Services\GamificationService;
use App\Services\GroupAccessService;
use App\Services\KnowledgebaseCommunityService;
use App\Services\NotificationService;
use App\Services\PointLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReactionController extends Controller
{
    public function toggle(
        Request $request,
        NotificationService $notifications,
        PointLedgerService $pointLedger,
        GamificationService $gamification
    )
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
        elseif ($type == 'knowledgebase') $dbType = KnowledgebaseCommunityService::REACTION_TYPE;
        elseif ($type == 'forum_comment') $dbType = 4;
        elseif ($type == 'directory_comment') $dbType = 44;
        elseif ($type == 'store_comment') $dbType = 444;
        elseif ($type == 'kb_comment') $dbType = KnowledgebaseCommunityService::COMMENT_REACTION_TYPE;
        elseif ($type == 'order') $dbType = 6;
        elseif ($type == 'order_comment') $dbType = 66;

        if ($dbType === 0) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        if (!$this->canReactToTarget($type, (int) $id, $user)) {
            return response()->json(['error' => __('messages.forum_unauthorized')], 403);
        }

        // Find existing like
        $existingLike = Like::where('uid', $uid)
                            ->where('sid', $id)
                            ->where('type', $dbType)
                            ->first();

        // Determine size and icon based on type
        $isComment = in_array($dbType, [4, 44, 444, KnowledgebaseCommunityService::COMMENT_REACTION_TYPE], true);
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
                    $this->removeReaction($existingLike, $existingOption, $user, $id, $dbType, $pointLedger);
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
                        'html' => '<img class="reaction-option-image" src="' . theme_asset('img/reaction/' . $reaction . '.png') . '" width="' . $imgSize . '" alt="reaction-' . $reaction . '">'
                    ];
                }
            } else {
                // New Reaction -> Toggle ON (Add)
                $this->addReaction($user, $id, $dbType, $reaction, $notifications, $pointLedger, $gamification);
                $result = [
                    'action' => 'added',
                    'html' => '<img class="reaction-option-image" src="' . theme_asset('img/reaction/' . $reaction . '.png') . '" width="' . $imgSize . '" alt="reaction-' . $reaction . '">'
                ];
            }

            DB::commit();
            return response()->json($result);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function removeReaction($like, $option, $user, $postId, $type, PointLedgerService $pointLedger)
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

            $pointLedger->award($ownerId, -1, 'reaction_removed_received', 'reaction_removed_received', 'reaction', (int) $postId);
            $pointLedger->award($user->id, -2, 'reaction_removed_given', 'reaction_removed_given', 'reaction', (int) $postId);
        }
    }

    private function addReaction(
        $user,
        $postId,
        $type,
        $reaction,
        NotificationService $notifications,
        PointLedgerService $pointLedger,
        GamificationService $gamification
    )
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
            } elseif ($type == 6) {
                $postUrl = "/orders/" . $postId;
            } elseif ($type == KnowledgebaseCommunityService::REACTION_TYPE) {
                $status = Status::find($postId);
                $article = $status?->related_content;
                if ($status && $article) {
                    $postUrl = route('kb.show', ['name' => $article->o_mode, 'article' => $article->name]);
                }
            } elseif ($type == KnowledgebaseCommunityService::COMMENT_REACTION_TYPE) {
                $comment = Option::find($postId);
                $status = $comment ? Status::find($comment->o_parent) : null;
                $article = $status?->related_content;
                if ($status && $article) {
                    $postUrl = route('kb.show', ['name' => $article->o_mode, 'article' => $article->name]) . "#comment_" . $postId;
                }
            } elseif ($type == 66) {
                $comment = \App\Models\Option::find($postId);
                if ($comment) {
                    $postUrl = "/orders/" . $comment->o_parent . "#comment_" . $postId;
                }
            }

            $message = __('messages.reaction_notification', ['user' => $user->username]);
            if (in_array($type, [4, 44, 444, KnowledgebaseCommunityService::COMMENT_REACTION_TYPE], true)) {
                $message = __('messages.reaction_notification', ['user' => $user->username]); // Fallback to same key, or we can add reaction_comment_notification later
            }
            
            $notifications->send($ownerId, $message, $postUrl, $reaction, $user->id, 'reaction');

            $pointLedger->award($ownerId, 1, 'reaction_received', 'reaction_received', 'reaction', (int) $postId);
            $pointLedger->award($user->id, 2, 'reaction_given', 'reaction_given', 'reaction', (int) $postId);
            $gamification->recordEvent($ownerId, 'reaction_received');
        }

        $gamification->recordEvent($user->id, 'reaction_given');
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
        } elseif ($type == 6) {
            // Order Request
            $order = \App\Models\OrderRequest::find($postId);
            return $order ? $order->uid : null;
        } elseif ($type == KnowledgebaseCommunityService::REACTION_TYPE) {
            $status = Status::find($postId);
            return $status ? $status->uid : null;
        } elseif ($type == KnowledgebaseCommunityService::COMMENT_REACTION_TYPE) {
            $comment = \App\Models\Option::find($postId);
            return $comment ? $comment->o_order : null;
        } elseif ($type == 66) {
            // Order Comment (Option)
            $comment = \App\Models\Option::find($postId);
            return $comment ? $comment->o_order : null;
        }
        return null;
    }

    private function canReactToTarget(string $type, int $id, User $user): bool
    {
        if ($type === 'forum') {
            $topic = ForumTopic::find($id);
            if (!$topic) {
                return false;
            }

            if ((int) $topic->group_id > 0) {
                return app(GroupAccessService::class)->canPostToGroup($topic->group()->first(), $user);
            }

            return true;
        }

        if ($type === 'forum_comment') {
            $comment = ForumComment::find($id);
            if (!$comment) {
                return false;
            }

            $topic = ForumTopic::find($comment->tid);
            if (!$topic) {
                return false;
            }

            if ((int) $topic->group_id > 0) {
                return app(GroupAccessService::class)->canPostToGroup($topic->group()->first(), $user);
            }
        }

        return true;
    }
}
