<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\Like;
use App\Models\Option;
use App\Models\Quest;
use App\Models\QuestProgress;
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

        $this->refreshBadges($userId);
    }

    public function refreshBadges(int $userId): void
    {
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
            default => 0,
        };
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

