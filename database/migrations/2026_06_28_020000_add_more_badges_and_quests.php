<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $badges = [
            [
                'slug' => 'top-client',
                'name_key' => 'badge_top_client_name',
                'description_key' => 'badge_top_client_desc',
                'icon' => 'fa-briefcase',
                'color' => 'blue',
                'points_reward' => 150,
                'criteria_type' => 'order_requests_count',
                'criteria_target' => 10,
                'sort_order' => 18
            ],
            [
                'slug' => 'helpful-seller',
                'name_key' => 'badge_helpful_seller_name',
                'description_key' => 'badge_helpful_seller_desc',
                'icon' => 'fa-star',
                'color' => 'gold',
                'points_reward' => 200,
                'criteria_type' => 'five_star_ratings',
                'criteria_target' => 10,
                'sort_order' => 19
            ],
            [
                'slug' => 'trendsetter',
                'name_key' => 'badge_trendsetter_name',
                'description_key' => 'badge_trendsetter_desc',
                'icon' => 'fa-fire',
                'color' => 'red',
                'points_reward' => 150,
                'criteria_type' => 'followers_count',
                'criteria_target' => 100,
                'sort_order' => 20
            ],
            [
                'slug' => 'prolific-writer',
                'name_key' => 'badge_prolific_writer_name',
                'description_key' => 'badge_prolific_writer_desc',
                'icon' => 'fa-pen-nib',
                'color' => 'teal',
                'points_reward' => 100,
                'criteria_type' => 'post_count',
                'criteria_target' => 100,
                'sort_order' => 21
            ],
            [
                'slug' => 'top-commenter',
                'name_key' => 'badge_top_commenter_name',
                'description_key' => 'badge_top_commenter_desc',
                'icon' => 'fa-comments',
                'color' => 'indigo',
                'points_reward' => 100,
                'criteria_type' => 'comment_count',
                'criteria_target' => 100,
                'sort_order' => 22
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                array_merge($badge, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $quests = [
            [
                'slug' => 'daily-login',
                'name_key' => 'quest_daily_login_name',
                'description_key' => 'quest_daily_login_desc',
                'event_key' => 'login',
                'target_count' => 1,
                'reward_points' => 5,
                'is_active' => 1,
                'period' => 'daily',
                'icon' => 'svg-status',
            ],
            [
                'slug' => 'daily-reaction',
                'name_key' => 'quest_daily_reaction_name',
                'description_key' => 'quest_daily_reaction_desc',
                'event_key' => 'reaction_given',
                'target_count' => 5,
                'reward_points' => 10,
                'is_active' => 1,
                'period' => 'daily',
                'icon' => 'svg-thumbs-up',
            ],
            [
                'slug' => 'weekly-post',
                'name_key' => 'quest_weekly_post_name',
                'description_key' => 'quest_weekly_post_desc',
                'event_key' => 'post_created',
                'target_count' => 5,
                'reward_points' => 20,
                'is_active' => 1,
                'period' => 'weekly',
                'icon' => 'svg-status',
            ],
            [
                'slug' => 'weekly-comment',
                'name_key' => 'quest_weekly_comment_name',
                'description_key' => 'quest_weekly_comment_desc',
                'event_key' => 'comment_created',
                'target_count' => 10,
                'reward_points' => 25,
                'is_active' => 1,
                'period' => 'weekly',
                'icon' => 'svg-comment',
            ],
            [
                'slug' => 'weekly-reaction-received',
                'name_key' => 'quest_weekly_reaction_received_name',
                'description_key' => 'quest_weekly_reaction_received_desc',
                'event_key' => 'reaction_received',
                'target_count' => 10,
                'reward_points' => 30,
                'is_active' => 1,
                'period' => 'weekly',
                'icon' => 'svg-thumbs-up',
            ],
        ];

        foreach ($quests as $quest) {
            DB::table('quests')->updateOrInsert(
                ['slug' => $quest['slug']],
                array_merge($quest, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        $badgeSlugs = [
            'top-client', 'helpful-seller', 'trendsetter', 'prolific-writer', 'top-commenter'
        ];
        DB::table('badges')->whereIn('slug', $badgeSlugs)->delete();

        $questSlugs = [
            'daily-login', 'daily-reaction', 'weekly-post', 'weekly-comment', 'weekly-reaction-received'
        ];
        DB::table('quests')->whereIn('slug', $questSlugs)->delete();
    }
};
