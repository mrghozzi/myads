<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Option;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReactionService
{
    public const ALLOWED_REACTIONS = ['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'];

    public function __construct(
        private readonly NotificationService $notifications,
        private readonly PointLedgerService $pointLedger,
        private readonly GamificationService $gamification
    ) {
    }

    /**
     * Map mixed string/int type to standard integer DB type code.
     */
    public function resolveDbType(int|string $type): int
    {
        if (is_numeric($type)) {
            $intType = (int) $type;
            return in_array($intType, [2, 22, 3, 4, 44, 444, 6, 66, 14, KnowledgebaseCommunityService::REACTION_TYPE, KnowledgebaseCommunityService::COMMENT_REACTION_TYPE], true)
                ? $intType
                : 0;
        }

        return match ((string) $type) {
            'forum', 'topic' => 2,
            'directory', 'site' => 22,
            'store', 'product' => 3,
            'knowledgebase', 'kb' => KnowledgebaseCommunityService::REACTION_TYPE,
            'forum_comment' => 4,
            'directory_comment' => 44,
            'store_comment' => 444,
            'kb_comment' => KnowledgebaseCommunityService::COMMENT_REACTION_TYPE,
            'order' => 6,
            'order_comment' => 66,
            'clips', 'clip' => 14,
            default => 0,
        };
    }

    /**
     * Toggle reaction for user on a given target.
     */
    public function toggle(User $user, int $id, int|string $rawType, ?string $reactionName = 'like'): array
    {
        $reaction = strtolower(trim((string) ($reactionName ?: 'like')));
        if (!in_array($reaction, self::ALLOWED_REACTIONS, true)) {
            return [
                'success' => false,
                'status_code' => 400,
                'error' => __('messages.error_occurred')
            ];
        }

        $dbType = $this->resolveDbType($rawType);
        if ($dbType === 0) {
            return [
                'success' => false,
                'status_code' => 400,
                'error' => __('messages.error_occurred')
            ];
        }

        if (!$this->canReactToTarget($rawType, $id, $user)) {
            return [
                'success' => false,
                'status_code' => 403,
                'error' => __('messages.forum_unauthorized')
            ];
        }

        $isComment = in_array($dbType, [4, 44, 444, KnowledgebaseCommunityService::COMMENT_REACTION_TYPE, 66], true);
        $imgSize = $isComment ? 16 : 30;
        $defaultIcon = $isComment
            ? '<i class="fa fa-thumbs-up" aria-hidden="true"></i>'
            : '<svg class="post-option-icon icon-thumbs-up"><use xlink:href="#svg-thumbs-up"></use></svg>';

        DB::beginTransaction();
        try {
            $existingLike = Like::where('uid', $user->id)
                ->where('sid', $id)
                ->where('type', $dbType)
                ->first();

            if ($existingLike) {
                $existingOption = Option::where('o_parent', $existingLike->id)
                    ->where('o_type', 'data_reaction')
                    ->first();

                $currentReaction = $existingOption ? $existingOption->o_valuer : 'like';

                if ($currentReaction === $reaction) {
                    // Same reaction -> Toggle OFF (Remove)
                    $this->removeReaction($existingLike, $existingOption, $user, $id, $dbType);
                    $result = [
                        'success' => true,
                        'action' => 'removed',
                        'reacted' => false,
                        'reaction' => null,
                        'html' => $defaultIcon,
                        'message' => 'Reaction removed'
                    ];
                } else {
                    // Different reaction -> Update
                    if ($existingOption) {
                        $existingOption->update([
                            'name' => $reaction,
                            'o_valuer' => $reaction
                        ]);
                    } else {
                        Option::create([
                            'name' => $reaction,
                            'o_type' => 'data_reaction',
                            'o_order' => $user->id,
                            'o_parent' => $existingLike->id,
                            'o_valuer' => $reaction,
                            'o_mode' => time()
                        ]);
                    }
                    $result = [
                        'success' => true,
                        'action' => 'updated',
                        'reacted' => true,
                        'reaction' => $reaction,
                        'html' => '<img class="reaction-option-image" src="' . theme_asset('img/reaction/' . $reaction . '.png') . '" width="' . $imgSize . '" alt="reaction-' . $reaction . '">',
                        'message' => 'Reaction updated'
                    ];
                }
            } else {
                // New Reaction -> Toggle ON (Add)
                $this->addReaction($user, $id, $dbType, $reaction);
                $result = [
                    'success' => true,
                    'action' => 'added',
                    'reacted' => true,
                    'reaction' => $reaction,
                    'html' => '<img class="reaction-option-image" src="' . theme_asset('img/reaction/' . $reaction . '.png') . '" width="' . $imgSize . '" alt="reaction-' . $reaction . '">',
                    'message' => 'Reaction added'
                ];
            }

            DB::commit();
            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return [
                'success' => false,
                'status_code' => 500,
                'error' => __('messages.error_occurred')
            ];
        }
    }

    private function removeReaction(Like $like, ?Option $option, User $user, int $postId, int $type): void
    {
        $time_t = $like->time_t;

        $like->delete();
        if ($option) {
            $option->delete();
        }

        $ownerId = $this->getPostOwnerId($postId, $type);
        if ($ownerId && $ownerId != $user->id) {
            Notification::where('uid', $ownerId)
                ->where('time', $time_t)
                ->where('state', 1)
                ->delete();

            $this->pointLedger->award($ownerId, -1, 'reaction_removed_received', 'reaction_removed_received', 'reaction', $postId);
            $this->pointLedger->award($user->id, -2, 'reaction_removed_given', 'reaction_removed_given', 'reaction', $postId);
        }
    }

    private function addReaction(User $user, int $postId, int $type, string $reaction): void
    {
        $time = time();

        $like = Like::create([
            'uid' => $user->id,
            'sid' => $postId,
            'type' => $type,
            'time_t' => $time,
        ]);

        Option::create([
            'name' => $reaction,
            'o_type' => 'data_reaction',
            'o_order' => $user->id,
            'o_parent' => $like->id,
            'o_valuer' => $reaction,
            'o_mode' => $time
        ]);

        $ownerId = $this->getPostOwnerId($postId, $type);
        if ($ownerId && $ownerId != $user->id) {
            $postUrl = $this->resolvePostUrl($postId, $type);
            $message = __('messages.reaction_notification', ['user' => $user->username]);

            $this->notifications->send($ownerId, $message, $postUrl, $reaction, $user->id, 'reaction');

            $this->pointLedger->award($ownerId, 1, 'reaction_received', 'reaction_received', 'reaction', $postId);
            $this->pointLedger->award($user->id, 2, 'reaction_given', 'reaction_given', 'reaction', $postId);
            $this->gamification->recordEvent($ownerId, 'reaction_received');
        }

        $this->gamification->recordEvent($user->id, 'reaction_given');
    }

    private function resolvePostUrl(int $postId, int $type): string
    {
        if ($type == 2) return "/t" . $postId;
        if ($type == 22) return "/dr" . $postId;
        if ($type == 14) return "/clips";
        if ($type == 6) return "/orders/" . $postId;

        if ($type == 3) {
            $product = \App\Models\Product::find($postId);
            return $product ? "/store/" . $product->name : "/store";
        }

        if ($type == 4) {
            $comment = ForumComment::find($postId);
            return $comment ? "/t" . $comment->tid . "#comment_" . $postId : "/forum";
        }

        if ($type == 44) {
            $comment = Option::find($postId);
            return $comment ? "/dr" . $comment->o_parent . "#comment_" . $postId : "/directory";
        }

        if ($type == 444) {
            $comment = Option::find($postId);
            if ($comment) {
                $product = \App\Models\Product::find($comment->o_parent);
                return $product ? "/store/" . $product->name . "#comment_" . $postId : "/store";
            }
        }

        if ($type == KnowledgebaseCommunityService::REACTION_TYPE) {
            $status = Status::find($postId);
            $article = $status?->related_content;
            return ($status && $article) ? route('kb.show', ['name' => $article->o_mode, 'article' => $article->name]) : "/portal";
        }

        if ($type == KnowledgebaseCommunityService::COMMENT_REACTION_TYPE) {
            $comment = Option::find($postId);
            $status = $comment ? Status::find($comment->o_parent) : null;
            $article = $status?->related_content;
            return ($status && $article) ? route('kb.show', ['name' => $article->o_mode, 'article' => $article->name]) . "#comment_" . $postId : "/portal";
        }

        if ($type == 66) {
            $comment = Option::find($postId);
            return $comment ? "/orders/" . $comment->o_parent . "#comment_" . $postId : "/orders";
        }

        return "/portal";
    }

    private function getPostOwnerId(int $postId, int $type): ?int
    {
        if ($type == 2 || $type == 14) {
            $topic = ForumTopic::find($postId);
            return $topic ? (int) $topic->uid : null;
        }

        if ($type == 22) {
            $site = Directory::find($postId);
            return $site ? (int) $site->uid : null;
        }

        if ($type == 3) {
            $product = \App\Models\Product::find($postId);
            return $product ? (int) $product->o_parent : null;
        }

        if ($type == 4) {
            $comment = ForumComment::find($postId);
            return $comment ? (int) $comment->uid : null;
        }

        if (in_array($type, [44, 444, 66, KnowledgebaseCommunityService::COMMENT_REACTION_TYPE], true)) {
            $comment = Option::find($postId);
            return $comment ? (int) $comment->o_order : null;
        }

        if ($type == 6) {
            $order = \App\Models\OrderRequest::find($postId);
            return $order ? (int) $order->uid : null;
        }

        if ($type == KnowledgebaseCommunityService::REACTION_TYPE) {
            $status = Status::find($postId);
            return $status ? (int) $status->uid : null;
        }

        return null;
    }

    private function canReactToTarget(int|string $type, int $id, User $user): bool
    {
        $dbType = $this->resolveDbType($type);

        if ($dbType === 2) {
            $topic = ForumTopic::find($id);
            if (!$topic) return false;
            if ((int) $topic->group_id > 0) {
                return app(GroupAccessService::class)->canPostToGroup($topic->group()->first(), $user);
            }
            return true;
        }

        if ($dbType === 4) {
            $comment = ForumComment::find($id);
            if (!$comment) return false;
            $topic = ForumTopic::find($comment->tid);
            if (!$topic) return false;
            if ((int) $topic->group_id > 0) {
                return app(GroupAccessService::class)->canPostToGroup($topic->group()->first(), $user);
            }
        }

        return true;
    }
}
