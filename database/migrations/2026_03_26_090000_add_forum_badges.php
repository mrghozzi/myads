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
                'slug' => 'forum-veteran',
                'name_key' => 'badge_forum_veteran_name',
                'description_key' => 'badge_forum_veteran_desc',
                'icon' => 'fa-crown',
                'color' => 'gold',
                'points_reward' => 300,
                'criteria_type' => 'forum_topics_count',
                'criteria_target' => 50,
                'sort_order' => 30
            ],
            [
                'slug' => 'helpful-member',
                'name_key' => 'badge_helpful_member_name',
                'description_key' => 'badge_helpful_member_desc',
                'icon' => 'fa-hands-helping',
                'color' => 'green',
                'points_reward' => 200,
                'criteria_type' => 'forum_replies_count',
                'criteria_target' => 100,
                'sort_order' => 31
            ],
            [
                'slug' => 'topic-starter',
                'name_key' => 'badge_topic_starter_name',
                'description_key' => 'badge_topic_starter_desc',
                'icon' => 'fa-lightbulb',
                'color' => 'yellow',
                'points_reward' => 100,
                'criteria_type' => 'unique_categories_topics',
                'criteria_target' => 10,
                'sort_order' => 32
            ],
            [
                'slug' => 'expert-moderator',
                'name_key' => 'badge_forum_moderator_trainee_name',
                'description_key' => 'badge_forum_moderator_trainee_desc',
                'icon' => 'fa-gavel',
                'color' => 'red',
                'points_reward' => 250,
                'criteria_type' => 'moderation_actions_count',
                'criteria_target' => 5,
                'sort_order' => 33
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
    }

    public function down(): void
    {
        $slugs = ['forum-veteran', 'helpful-member', 'topic-starter', 'expert-moderator'];
        DB::table('badges')->whereIn('slug', $slugs)->delete();
    }
};
