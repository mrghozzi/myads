<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $quests = [
            [
                'slug' => 'new-connections',
                'name_key' => 'quest_new_connections_name',
                'description_key' => 'quest_new_connections_desc',
                'event_key' => 'follow_created',
                'target_count' => 5,
                'reward_points' => 15,
                'is_active' => 1,
                'period' => 'weekly',
            ],
            [
                'slug' => 'forum-starter',
                'name_key' => 'quest_forum_starter_name',
                'description_key' => 'quest_forum_starter_desc',
                'event_key' => 'forum_topic_created',
                'target_count' => 3,
                'reward_points' => 20,
                'is_active' => 1,
                'period' => 'weekly',
            ],
            [
                'slug' => 'web-explorer',
                'name_key' => 'quest_web_explorer_name',
                'description_key' => 'quest_web_explorer_desc',
                'event_key' => 'directory_submission_created',
                'target_count' => 2,
                'reward_points' => 25,
                'is_active' => 1,
                'period' => 'weekly',
            ],
            [
                'slug' => 'tool-collector',
                'name_key' => 'quest_tool_collector_name',
                'description_key' => 'quest_tool_collector_desc',
                'event_key' => 'product_downloaded',
                'target_count' => 5,
                'reward_points' => 10,
                'is_active' => 1,
                'period' => 'daily',
            ],
            [
                'slug' => 'service-helper',
                'name_key' => 'quest_service_helper_name',
                'description_key' => 'quest_service_helper_desc',
                'event_key' => 'order_offer_created',
                'target_count' => 3,
                'reward_points' => 30,
                'is_active' => 1,
                'period' => 'weekly',
            ],
        ];

        foreach ($quests as $quest) {
            DB::table('quests')->updateOrInsert(
                ['slug' => $quest['slug']],
                array_merge($quest, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('quests')->whereIn('slug', [
            'new-connections',
            'forum-starter',
            'web-explorer',
            'tool-collector',
            'service-helper',
        ])->delete();
    }
};
