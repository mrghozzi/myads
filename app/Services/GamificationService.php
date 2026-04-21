<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\Like;
use App\Models\Option;
use App\Models\OrderContract;
use App\Models\OrderOffer;
use App\Models\Quest;
use App\Models\QuestProgress;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    public function __construct(
        private readonly PointLedgerService $pointLedger,
        private readonly NotificationService $notifications,
        private readonly V420SchemaService $schema
    ) {
    }

    public function recordEvent(int $userId, string $eventKey, int $count = 1): void
    {
        try {
            if ($userId <= 0) {
                return;
            }

            $user = User::find($userId);
            if (!$user) {
                return;
            }

            $quests = $this->schema->supports('quests')
                ? Quest::where('is_active', true)
                    ->where('event_key', $eventKey)
                    ->get()
                : collect();

            foreach ($quests as $quest) {
                $periodKey = $this->periodKeyFor($quest->period);
                $progress = QuestProgress::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'quest_id' => $quest->id,
                        'period_key' => $periodKey,
                    ],
                    [
                        'progress' => 0,
                    ]
                );

                if ($progress->completed_at) {
                    continue;
                }

                $progress->progress += $count;
                if ($progress->progress >= (int) $quest->target_count) {
                    $progress->progress = (int) $quest->target_count;
                    $progress->completed_at = now();
                }
                $progress->save();

                if ($progress->completed_at && !$progress->rewarded_at) {
                    $this->pointLedger->award(
                        $userId,
                        (float) $quest->reward_points,
                        'quest_reward',
                        $quest->slug,
                        'quest',
                        $quest->id
                    );
                    $progress->rewarded_at = now();
                    $progress->save();

                    $this->notifications->send(
                        $userId,
                        __('messages.quest_completed_notification', ['quest' => __('messages.' . $quest->name_key)]),
                        '/history',
                        'notification'
                    );
                }
            }

            if ($eventKey === 'visit_exchange_completed') {
                Option::create([
                    'name' => 'Visit Exchange',
                    'o_valuer' => '1',
                    'o_type' => 'v_visited',
                    'o_order' => $userId,
                    'o_mode' => time(),
                ]);
            }

            $this->refreshBadges($userId);
        } catch (\Throwable $e) {
            \Log::error('Gamification recordEvent Error: ' . $e->getMessage(), [
                'userId' => $userId,
                'eventKey' => $eventKey
            ]);
        }
    }

    public function refreshBadges(int $userId): void
    {
        try {
            if (!$this->schema->supports('badges')) {
                return;
            }

            $user = User::find($userId);
            if (!$user) {
                return;
            }

            $badges = Badge::where('is_active', true)->orderBy('sort_order')->get();
            foreach ($badges as $badge) {
                $progress = $this->progressForBadge($user, $badge);

                $userBadge = UserBadge::firstOrNew([
                    'user_id' => $userId,
                    'badge_id' => $badge->id,
                ]);

                $alreadyUnlocked = $userBadge->exists && $userBadge->unlocked_at !== null;

                $userBadge->progress = min((int) $badge->criteria_target, $progress);

                if ($progress >= (int) $badge->criteria_target && !$alreadyUnlocked) {
                    $userBadge->unlocked_at = now();
                }

                $userBadge->save();

                if (!$alreadyUnlocked && $userBadge->unlocked_at) {
                    $this->pointLedger->award(
                        $userId,
                        (float) $badge->points_reward,
                        'badge_reward',
                        $badge->slug,
                        'badge',
                        $badge->id
                    );

                    $this->notifications->send(
                        $userId,
                        __('messages.badge_unlocked_notification', ['badge' => __('messages.' . $badge->name_key)]),
                        '/settings/badges',
                        'notification'
                    );
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Gamification refreshBadges Error: ' . $e->getMessage(), ['userId' => $userId]);
        }
    }

    private function periodKeyFor(string $period): string
    {
        return $period === 'weekly' ? now()->format('o-\WW') : now()->format('Y-m-d');
    }

    private function progressForBadge(User $user, Badge $badge): int
    {
        return match ($badge->criteria_type) {
            'complete_profile' => ($user->email && $user->img && trim((string) $user->sig) !== '') ? 1 : 0,
            'post_count' => Status::where('uid', $user->id)->whereIn('s_type', [100, 4])->count(),
            'image_post_count' => Status::where('uid', $user->id)->where('s_type', 4)->count(),
            'link_preview_count' => $this->schema->supports('link_previews')
                ? Status::where('uid', $user->id)->whereHas('linkPreviewRecord')->count()
                : 0,
            'comment_count' => (int) ForumComment::where('uid', $user->id)->count()
                + (int) Option::where('o_order', $user->id)->whereIn('o_type', ['d_coment', 's_coment'])->count(),
            'repost_count' => $this->schema->supports('reposts')
                ? StatusRepost::where('user_id', $user->id)->count()
                : 0,
            'reactions_received' => $this->reactionsReceived($user),
            'followers_count' => Like::where('sid', $user->id)->where('type', 1)->count(),
            'following_count' => Like::where('uid', $user->id)->where('type', 1)->count(),
            'product_count' => Option::where('o_parent', $user->id)->where('o_type', 'store')->count(),
            'directory_count' => Directory::where('uid', $user->id)->count(),
            'visit_exchanges' => Option::where('o_order', $user->id)->where('o_type', 'v_visited')->count(),
            'points_balance' => (int) $user->pts,
            'ads_count' => DB::table('banner')->where('uid', $user->id)->count() + DB::table('link')->where('uid', $user->id)->count(),
            'kb_articles' => Option::where('o_parent', $user->id)->where('o_type', 'knowledgebase')->where('o_order', 0)->count(),
            'night_posts' => $this->countNightPosts($user->id),
            'order_requests_count' => OrderRequest::where('uid', $user->id)->count(),
            'order_bids_count' => $this->schema->hasTable('order_offers')
                ? OrderOffer::where('user_id', $user->id)->where('status', '!=', OrderOffer::STATUS_WITHDRAWN)->count()
                : Option::where('o_order', $user->id)->where('o_type', 'o_order')->count(),
            'best_offers_won' => $this->schema->hasTable('order_contracts')
                ? OrderContract::where('provider_user_id', $user->id)->count()
                : OrderRequest::whereIn('best_offer_id', function ($query) use ($user) {
                    $query->select('id')->from('options')->where('o_type', 'o_order')->where('o_order', $user->id);
                })->count(),
            'five_star_ratings' => $this->schema->hasTable('order_offers')
                ? OrderOffer::where('user_id', $user->id)->where('client_rating', 5)->count()
                : Option::where('o_order', $user->id)
                    ->where('o_type', 'o_order')
                    ->where('o_mode', 5)
                    ->count(),
            'forum_topics_count' => DB::table('forum')->where('uid', $user->id)->count(),
            'forum_replies_count' => ForumComment::where('uid', $user->id)->count(),
            'unique_categories_topics' => DB::table('forum')->where('uid', $user->id)->distinct('cat')->count('cat'),
            'moderation_actions_count' => Option::where('o_order', $user->id)
                ->whereIn('o_type', ['forum_pin', 'forum_lock'])
                ->count(),
            default => 0,
        };
    }

    private function countNightPosts(int $userId): int
    {
        return Status::where('uid', $userId)
            ->whereIn('s_type', [100, 4])
            ->get()
            ->filter(function ($status) {
                $hour = date('G', (int) $status->date);
                return $hour >= 0 && $hour < 5;
            })
            ->count();
    }

    private function reactionsReceived(User $user): int
    {
        $forumTopicIds = DB::table('forum')->where('uid', $user->id)->pluck('id');
        $directoryIds = Directory::where('uid', $user->id)->pluck('id');
        $forumCommentIds = ForumComment::where('uid', $user->id)->pluck('id');
        $optionCommentIds = Option::where('o_order', $user->id)
            ->whereIn('o_type', ['d_coment', 's_coment'])
            ->pluck('id');

        return Like::query()
            ->when($forumTopicIds->isNotEmpty(), fn ($query) => $query->orWhere(function ($nested) use ($forumTopicIds) {
                $nested->where('type', 2)->whereIn('sid', $forumTopicIds);
            }))
            ->when($directoryIds->isNotEmpty(), fn ($query) => $query->orWhere(function ($nested) use ($directoryIds) {
                $nested->where('type', 22)->whereIn('sid', $directoryIds);
            }))
            ->when($forumCommentIds->isNotEmpty(), fn ($query) => $query->orWhere(function ($nested) use ($forumCommentIds) {
                $nested->where('type', 4)->whereIn('sid', $forumCommentIds);
            }))
            ->when($optionCommentIds->isNotEmpty(), fn ($query) => $query->orWhere(function ($nested) use ($optionCommentIds) {
                $nested->whereIn('type', [44, 444])->whereIn('sid', $optionCommentIds);
            }))
            ->count();
    }

    public function repairQuestData(): void
    {
        if (!$this->schema->supports('quests')) {
            return;
        }

        $map = [
            // Legacy Quests
            'daily_first_post'              => 'svg-status',
            'daily_three_comments'          => 'svg-comment',
            'daily_five_reactions_given'    => 'svg-thumbs-up',
            'daily_five_visit_exchanges'    => 'svg-timeline',
            'weekly_three_reposts'          => 'svg-share',
            'weekly_ten_reactions_received' => 'svg-thumbs-up',

            // New Premium Quests
            'new-connections'               => 'svg-members',
            'forum-starter'                 => 'svg-forum',
            'web-explorer'                  => 'svg-list-grid-view',
            'tool-collector'                => 'svg-marketplace',
            'service-helper'                => 'svg-ticket',
        ];

        foreach ($map as $slug => $icon) {
            DB::table('quests')->where('slug', $slug)->update(['icon' => $icon]);
        }

        // Fix zero or negative target counts
        DB::table('quests')->where('target_count', '<=', 0)->update(['target_count' => 1]);
        
        // Ensure first-post is at least 1 (legacy safety)
        DB::table('quests')->where('slug', 'daily_first_post')->where('target_count', '<=', 0)->update(['target_count' => 1]);
    }
}
