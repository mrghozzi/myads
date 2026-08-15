<?php

namespace App\Services;

use App\Models\ForumComment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Option;
use App\Models\State;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrphanCleanupService
{
    /**
     * Diagnose and count all orphaned items in the system.
     *
     * @return array{
     *     records: array{follows: int, user_reactions: int, notifications: int, total: int},
     *     content: array{comments: int, reactions: int, data_reactions: int, aux_files: int, total: int},
     *     stats: array{banner_stats: int, link_stats: int, smart_stats: int, orphaned_visits: int, total: int}
     * }
     */
    public function diagnoseOrphans(): array
    {
        // 1. Orphaned Records Diagnostics
        $orphanedFollows = Like::query()
            ->where('type', 1)
            ->where(function ($q) {
                $q->whereNotIn('uid', DB::table('users')->select('id'))
                  ->orWhereNotIn('sid', DB::table('users')->select('id'));
            })
            ->count();

        $orphanedUserReactions = Like::query()
            ->where('type', '!=', 1)
            ->whereNotIn('uid', DB::table('users')->select('id'))
            ->count();

        $orphanedNotifications = Notification::query()
            ->whereNotIn('uid', DB::table('users')->select('id'))
            ->count();

        $totalRecords = $orphanedFollows + $orphanedUserReactions + $orphanedNotifications;

        // 2. Orphaned Content Diagnostics
        $orphanedForumComments = Schema::hasTable('forum') && Schema::hasTable('f_coment')
            ? ForumComment::query()
                ->whereNotIn('tid', DB::table('forum')->select('id'))
                ->count()
            : 0;

        $orphanedDirectoryComments = Schema::hasTable('directory')
            ? Option::query()
                ->where('o_type', 'd_coment')
                ->whereNotIn('o_parent', DB::table('directory')->select('id'))
                ->count()
            : 0;

        $orphanedStoreComments = Option::query()
            ->where('o_type', 's_coment')
            ->whereNotIn('o_parent', DB::table('options')->where('o_type', 'store')->select('id'))
            ->count();

        $orphanedOrderComments = Schema::hasTable('order_requests')
            ? Option::query()
                ->where('o_type', 'order_comment')
                ->whereNotIn('o_parent', DB::table('order_requests')->select('id'))
                ->count()
            : 0;

        $orphanedKbComments = Schema::hasTable('status')
            ? Option::query()
                ->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)
                ->whereNotIn('o_parent', DB::table('status')->select('id'))
                ->count()
            : 0;

        $totalComments = $orphanedForumComments + $orphanedDirectoryComments + $orphanedStoreComments + $orphanedOrderComments + $orphanedKbComments;

        // Reactions on deleted content items
        $orphanedForumTopicReactions = Schema::hasTable('forum')
            ? Like::query()->where('type', 2)->whereNotIn('sid', DB::table('forum')->select('id'))->count()
            : 0;

        $orphanedDirectoryReactions = Schema::hasTable('directory')
            ? Like::query()->where('type', 22)->whereNotIn('sid', DB::table('directory')->select('id'))->count()
            : 0;

        $orphanedStoreReactions = Like::query()->where('type', 3)
            ->whereNotIn('sid', DB::table('options')->where('o_type', 'store')->select('id'))
            ->count();

        $orphanedOrderReactions = Schema::hasTable('order_requests')
            ? Like::query()->where('type', 6)->whereNotIn('sid', DB::table('order_requests')->select('id'))->count()
            : 0;

        $orphanedForumCommentReactions = Schema::hasTable('f_coment')
            ? Like::query()->where('type', 4)->whereNotIn('sid', DB::table('f_coment')->select('id'))->count()
            : 0;

        $orphanedDirCommentReactions = Like::query()->where('type', 44)
            ->whereNotIn('sid', DB::table('options')->where('o_type', 'd_coment')->select('id'))
            ->count();

        $orphanedStoreCommentReactions = Like::query()->where('type', 444)
            ->whereNotIn('sid', DB::table('options')->where('o_type', 's_coment')->select('id'))
            ->count();

        $orphanedOrderCommentReactions = Like::query()->where('type', 66)
            ->whereNotIn('sid', DB::table('options')->where('o_type', 'order_comment')->select('id'))
            ->count();

        $orphanedKbReactions = Schema::hasTable('status')
            ? Like::query()->where('type', KnowledgebaseCommunityService::REACTION_TYPE)->whereNotIn('sid', DB::table('status')->select('id'))->count()
            : 0;

        $orphanedKbCommentReactions = Like::query()->where('type', KnowledgebaseCommunityService::COMMENT_REACTION_TYPE)
            ->whereNotIn('sid', DB::table('options')->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)->select('id'))
            ->count();

        $totalContentReactions = $orphanedForumTopicReactions + $orphanedDirectoryReactions + $orphanedStoreReactions +
            $orphanedOrderReactions + $orphanedForumCommentReactions + $orphanedDirCommentReactions +
            $orphanedStoreCommentReactions + $orphanedOrderCommentReactions + $orphanedKbReactions + $orphanedKbCommentReactions;

        // Orphaned data_reaction (where like id doesn't exist)
        $orphanedDataReactions = Option::query()
            ->where('o_type', 'data_reaction')
            ->whereNotIn('o_parent', DB::table('like')->select('id'))
            ->count();

        // Orphaned store aux files & types
        $orphanedStoreFiles = Option::query()
            ->whereIn('o_type', ['store_file', 'store_type'])
            ->whereNotIn('o_parent', DB::table('options')->where('o_type', 'store')->select('id'))
            ->count();

        $totalContent = $totalComments + $totalContentReactions + $orphanedDataReactions + $orphanedStoreFiles;

        // 3. Orphaned Statistics Diagnostics
        $orphanedBannerStats = Schema::hasTable('banner') && Schema::hasTable('state')
            ? State::query()
                ->whereIn('t_name', ['banner', 'vu'])
                ->where(function ($q) {
                    $q->where('pid', '<=', 0)
                        ->orWhereNotIn('pid', DB::table('banner')->select('id'));
                })
                ->count()
            : 0;

        $orphanedLinkStats = Schema::hasTable('link') && Schema::hasTable('state')
            ? State::query()
                ->whereIn('t_name', ['link', 'clik'])
                ->where(function ($q) {
                    $q->where('pid', '<=', 0)
                        ->orWhereNotIn('pid', DB::table('link')->select('id'));
                })
                ->count()
            : 0;

        $orphanedSmartStats = Schema::hasTable('smart_ads') && Schema::hasTable('state')
            ? State::query()
                ->whereIn('t_name', ['smart', 'smart_click'])
                ->where(function ($q) {
                    $q->where('pid', '<=', 0)
                        ->orWhereNotIn('pid', DB::table('smart_ads')->select('id'));
                })
                ->count()
            : 0;

        $orphanedVisits = Schema::hasTable('visits')
            ? Visit::query()
                ->where('uid', '>', 0)
                ->whereNotIn('uid', DB::table('users')->select('id'))
                ->count()
            : 0;

        $totalStats = $orphanedBannerStats + $orphanedLinkStats + $orphanedSmartStats + $orphanedVisits;

        return [
            'records' => [
                'follows' => $orphanedFollows,
                'user_reactions' => $orphanedUserReactions,
                'notifications' => $orphanedNotifications,
                'total' => $totalRecords,
            ],
            'content' => [
                'comments' => $totalComments,
                'reactions' => $totalContentReactions,
                'data_reactions' => $orphanedDataReactions,
                'aux_files' => $orphanedStoreFiles,
                'total' => $totalContent,
            ],
            'stats' => [
                'banner_stats' => $orphanedBannerStats,
                'link_stats' => $orphanedLinkStats,
                'smart_stats' => $orphanedSmartStats,
                'orphaned_visits' => $orphanedVisits,
                'total' => $totalStats,
            ],
        ];
    }

    /**
     * Safely repair and clean orphaned records (user follows, reactions from deleted users, notifications).
     *
     * @return array{total: int, details: array<string, int>}
     */
    public function repairOrphanedRecords(): array
    {
        return DB::transaction(function () {
            $details = [];

            // 1. Follow records where either follower (uid) or followed (sid) no longer exists in users table
            $deletedFollowers = Like::query()
                ->where('type', 1)
                ->whereNotIn('uid', DB::table('users')->select('id'))
                ->delete();

            $deletedFollowed = Like::query()
                ->where('type', 1)
                ->whereNotIn('sid', DB::table('users')->select('id'))
                ->delete();

            $details['follows'] = $deletedFollowers + $deletedFollowed;

            // 2. Reactions given by deleted users (uid not in users table)
            // First, delete associated data_reaction options for these likes
            $deletedReactionDetails = Option::query()
                ->where('o_type', 'data_reaction')
                ->whereIn('o_parent', function ($sub) {
                    $sub->select('id')
                        ->from('like')
                        ->where('type', '!=', 1)
                        ->whereNotIn('uid', DB::table('users')->select('id'));
                })
                ->delete();

            $deletedUserReactions = Like::query()
                ->where('type', '!=', 1)
                ->whereNotIn('uid', DB::table('users')->select('id'))
                ->delete();

            $details['user_reactions'] = $deletedUserReactions + $deletedReactionDetails;

            // 3. Notifications targeting deleted users
            $deletedNotifications = Notification::query()
                ->whereNotIn('uid', DB::table('users')->select('id'))
                ->delete();

            $details['notifications'] = $deletedNotifications;

            $total = array_sum($details);

            Log::info('Orphaned records repair completed.', [
                'cleaned_total' => $total,
                'breakdown' => $details,
            ]);

            return [
                'total' => $total,
                'details' => $details,
            ];
        });
    }

    /**
     * Safely repair and clean orphaned content (comments, reactions on deleted content, store aux items).
     *
     * @return array{total: int, details: array<string, int>}
     */
    public function repairOrphanedContent(): array
    {
        return DB::transaction(function () {
            $details = [];

            // 1. Forum comments where forum topic was deleted
            if (Schema::hasTable('forum') && Schema::hasTable('f_coment')) {
                // Delete reactions on orphaned forum comments first
                Like::query()
                    ->where('type', 4)
                    ->whereIn('sid', function ($sub) {
                        $sub->select('id')
                            ->from('f_coment')
                            ->whereNotIn('tid', DB::table('forum')->select('id'));
                    })
                    ->delete();

                $details['forum_comments'] = ForumComment::query()
                    ->whereNotIn('tid', DB::table('forum')->select('id'))
                    ->delete();
            } else {
                $details['forum_comments'] = 0;
            }

            // 2. Directory comments where directory item was deleted
            if (Schema::hasTable('directory')) {
                Like::query()
                    ->where('type', 44)
                    ->whereIn('sid', function ($sub) {
                        $sub->select('id')
                            ->from('options')
                            ->where('o_type', 'd_coment')
                            ->whereNotIn('o_parent', DB::table('directory')->select('id'));
                    })
                    ->delete();

                $details['directory_comments'] = Option::query()
                    ->where('o_type', 'd_coment')
                    ->whereNotIn('o_parent', DB::table('directory')->select('id'))
                    ->delete();
            } else {
                $details['directory_comments'] = 0;
            }

            // 3. Store comments where product item was deleted
            Like::query()
                ->where('type', 444)
                ->whereIn('sid', function ($sub) {
                    $sub->select('id')
                        ->from('options')
                        ->where('o_type', 's_coment')
                        ->whereNotIn('o_parent', DB::table('options')->where('o_type', 'store')->select('id'));
                })
                ->delete();

            $details['store_comments'] = Option::query()
                ->where('o_type', 's_coment')
                ->whereNotIn('o_parent', DB::table('options')->where('o_type', 'store')->select('id'))
                ->delete();

            // 4. Order comments where order request was deleted
            if (Schema::hasTable('order_requests')) {
                Like::query()
                    ->where('type', 66)
                    ->whereIn('sid', function ($sub) {
                        $sub->select('id')
                            ->from('options')
                            ->where('o_type', 'order_comment')
                            ->whereNotIn('o_parent', DB::table('order_requests')->select('id'));
                    })
                    ->delete();

                $details['order_comments'] = Option::query()
                    ->where('o_type', 'order_comment')
                    ->whereNotIn('o_parent', DB::table('order_requests')->select('id'))
                    ->delete();
            } else {
                $details['order_comments'] = 0;
            }

            // 5. Knowledgebase comments where status was deleted
            if (Schema::hasTable('status')) {
                Like::query()
                    ->where('type', KnowledgebaseCommunityService::COMMENT_REACTION_TYPE)
                    ->whereIn('sid', function ($sub) {
                        $sub->select('id')
                            ->from('options')
                            ->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)
                            ->whereNotIn('o_parent', DB::table('status')->select('id'));
                    })
                    ->delete();

                $details['kb_comments'] = Option::query()
                    ->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)
                    ->whereNotIn('o_parent', DB::table('status')->select('id'))
                    ->delete();
            } else {
                $details['kb_comments'] = 0;
            }

            // 6. Content Reactions (topics, directory, products, orders, clips, kb)
            $reactionDeletions = 0;

            if (Schema::hasTable('forum')) {
                $reactionDeletions += Like::query()
                    ->whereIn('type', [2, 14])
                    ->whereNotIn('sid', DB::table('forum')->select('id'))
                    ->delete();
            }

            if (Schema::hasTable('directory')) {
                $reactionDeletions += Like::query()
                    ->where('type', 22)
                    ->whereNotIn('sid', DB::table('directory')->select('id'))
                    ->delete();
            }

            $reactionDeletions += Like::query()
                ->where('type', 3)
                ->whereNotIn('sid', DB::table('options')->where('o_type', 'store')->select('id'))
                ->delete();

            if (Schema::hasTable('order_requests')) {
                $reactionDeletions += Like::query()
                    ->where('type', 6)
                    ->whereNotIn('sid', DB::table('order_requests')->select('id'))
                    ->delete();
            }

            if (Schema::hasTable('status')) {
                $reactionDeletions += Like::query()
                    ->where('type', KnowledgebaseCommunityService::REACTION_TYPE)
                    ->whereNotIn('sid', DB::table('status')->select('id'))
                    ->delete();
            }

            // Also clean reactions on deleted comments
            if (Schema::hasTable('f_coment')) {
                $reactionDeletions += Like::query()
                    ->where('type', 4)
                    ->whereNotIn('sid', DB::table('f_coment')->select('id'))
                    ->delete();
            }

            $reactionDeletions += Like::query()
                ->where('type', 44)
                ->whereNotIn('sid', DB::table('options')->where('o_type', 'd_coment')->select('id'))
                ->delete();

            $reactionDeletions += Like::query()
                ->where('type', 444)
                ->whereNotIn('sid', DB::table('options')->where('o_type', 's_coment')->select('id'))
                ->delete();

            $reactionDeletions += Like::query()
                ->where('type', 66)
                ->whereNotIn('sid', DB::table('options')->where('o_type', 'order_comment')->select('id'))
                ->delete();

            $reactionDeletions += Like::query()
                ->where('type', KnowledgebaseCommunityService::COMMENT_REACTION_TYPE)
                ->whereNotIn('sid', DB::table('options')->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)->select('id'))
                ->delete();

            $details['content_reactions'] = $reactionDeletions;

            // 7. Orphaned data_reaction (where like id doesn't exist)
            $details['data_reactions'] = Option::query()
                ->where('o_type', 'data_reaction')
                ->whereNotIn('o_parent', DB::table('like')->select('id'))
                ->delete();

            // 8. Orphaned store auxiliary items (store_file, store_type)
            $details['store_aux_files'] = Option::query()
                ->whereIn('o_type', ['store_file', 'store_type'])
                ->whereNotIn('o_parent', DB::table('options')->where('o_type', 'store')->select('id'))
                ->delete();

            $total = array_sum($details);

            Log::info('Orphaned content repair completed.', [
                'cleaned_total' => $total,
                'breakdown' => $details,
            ]);

            return [
                'total' => $total,
                'details' => $details,
            ];
        });
    }

    /**
     * Safely repair and clean orphaned statistics & event logs.
     *
     * @return array{total: int, details: array<string, int>}
     */
    public function repairOrphanedStats(): array
    {
        return DB::transaction(function () {
            $details = [];

            // 1. Banner stats where banner is deleted or pid <= 0
            if (Schema::hasTable('banner') && Schema::hasTable('state')) {
                $details['banner_stats'] = State::query()
                    ->whereIn('t_name', ['banner', 'vu'])
                    ->where(function ($q) {
                        $q->where('pid', '<=', 0)
                            ->orWhereNotIn('pid', DB::table('banner')->select('id'));
                    })
                    ->delete();
            } else {
                $details['banner_stats'] = 0;
            }

            // 2. Link stats where link is deleted or pid <= 0
            if (Schema::hasTable('link') && Schema::hasTable('state')) {
                $details['link_stats'] = State::query()
                    ->whereIn('t_name', ['link', 'clik'])
                    ->where(function ($q) {
                        $q->where('pid', '<=', 0)
                            ->orWhereNotIn('pid', DB::table('link')->select('id'));
                    })
                    ->delete();
            } else {
                $details['link_stats'] = 0;
            }

            // 3. Smart ad stats where smart ad is deleted or pid <= 0
            if (Schema::hasTable('smart_ads') && Schema::hasTable('state')) {
                $details['smart_stats'] = State::query()
                    ->whereIn('t_name', ['smart', 'smart_click'])
                    ->where(function ($q) {
                        $q->where('pid', '<=', 0)
                            ->orWhereNotIn('pid', DB::table('smart_ads')->select('id'));
                    })
                    ->delete();
            } else {
                $details['smart_stats'] = 0;
            }

            // 4. Custom ad placement stats if table exists
            if (Schema::hasTable('custom_ad_events') && Schema::hasTable('custom_ad_placements')) {
                $details['custom_ad_stats'] = DB::table('custom_ad_events')
                    ->whereNotIn('placement_id', DB::table('custom_ad_placements')->select('id'))
                    ->delete();
            } else {
                $details['custom_ad_stats'] = 0;
            }

            // 5. Visits with deleted user id (where uid > 0 and user not found)
            if (Schema::hasTable('visits')) {
                $details['orphaned_visits'] = Visit::query()
                    ->where('uid', '>', 0)
                    ->whereNotIn('uid', DB::table('users')->select('id'))
                    ->delete();
            } else {
                $details['orphaned_visits'] = 0;
            }

            $total = array_sum($details);

            Log::info('Orphaned stats repair completed.', [
                'cleaned_total' => $total,
                'breakdown' => $details,
            ]);

            return [
                'total' => $total,
                'details' => $details,
            ];
        });
    }
}
