<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('badges')) {
            $badges = [
                [
                    'slug' => 'video-star',
                    'name_key' => 'badge_video_star_name',
                    'description_key' => 'badge_video_star_desc',
                    'icon' => 'fa-video',
                    'color' => 'red',
                    'points_reward' => 150,
                    'criteria_type' => 'video_post_count',
                    'criteria_target' => 10,
                    'sort_order' => 23,
                ],
                [
                    'slug' => 'clips-master',
                    'name_key' => 'badge_clips_master_name',
                    'description_key' => 'badge_clips_master_desc',
                    'icon' => 'fa-film',
                    'color' => 'purple',
                    'points_reward' => 150,
                    'criteria_type' => 'clips_post_count',
                    'criteria_target' => 15,
                    'sort_order' => 24,
                ],
                [
                    'slug' => 'audio-maestro',
                    'name_key' => 'badge_audio_maestro_name',
                    'description_key' => 'badge_audio_maestro_desc',
                    'icon' => 'fa-music',
                    'color' => 'gold',
                    'points_reward' => 120,
                    'criteria_type' => 'audio_post_count',
                    'criteria_target' => 10,
                    'sort_order' => 25,
                ],
                [
                    'slug' => 'resource-vault',
                    'name_key' => 'badge_resource_vault_name',
                    'description_key' => 'badge_resource_vault_desc',
                    'icon' => 'fa-folder-open',
                    'color' => 'teal',
                    'points_reward' => 120,
                    'criteria_type' => 'file_post_count',
                    'criteria_target' => 10,
                    'sort_order' => 26,
                ],
                [
                    'slug' => 'multimedia-pioneer',
                    'name_key' => 'badge_multimedia_pioneer_name',
                    'description_key' => 'badge_multimedia_pioneer_desc',
                    'icon' => 'fa-photo-video',
                    'color' => 'violet',
                    'points_reward' => 250,
                    'criteria_type' => 'multimedia_post_count',
                    'criteria_target' => 25,
                    'sort_order' => 27,
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

        if (Schema::hasTable('quests')) {
            $quests = [
                [
                    'slug' => 'daily-video-creator',
                    'name_key' => 'quest_daily_video_creator_name',
                    'description_key' => 'quest_daily_video_creator_desc',
                    'event_key' => 'video_post_created',
                    'target_count' => 1,
                    'reward_points' => 25,
                    'is_active' => 1,
                    'period' => 'daily',
                    'icon' => 'svg-status',
                ],
                [
                    'slug' => 'daily-clips-creator',
                    'name_key' => 'quest_daily_clips_creator_name',
                    'description_key' => 'quest_daily_clips_creator_desc',
                    'event_key' => 'clips_post_created',
                    'target_count' => 1,
                    'reward_points' => 20,
                    'is_active' => 1,
                    'period' => 'daily',
                    'icon' => 'svg-status',
                ],
                [
                    'slug' => 'weekly-multimedia-producer',
                    'name_key' => 'quest_weekly_multimedia_producer_name',
                    'description_key' => 'quest_weekly_multimedia_producer_desc',
                    'event_key' => 'multimedia_post_created',
                    'target_count' => 3,
                    'reward_points' => 50,
                    'is_active' => 1,
                    'period' => 'weekly',
                    'icon' => 'svg-status',
                ],
                [
                    'slug' => 'weekly-audio-sharer',
                    'name_key' => 'quest_weekly_audio_sharer_name',
                    'description_key' => 'quest_weekly_audio_sharer_desc',
                    'event_key' => 'audio_post_created',
                    'target_count' => 2,
                    'reward_points' => 35,
                    'is_active' => 1,
                    'period' => 'weekly',
                    'icon' => 'svg-status',
                ],
                [
                    'slug' => 'weekly-file-publisher',
                    'name_key' => 'quest_weekly_file_publisher_name',
                    'description_key' => 'quest_weekly_file_publisher_desc',
                    'event_key' => 'file_post_created',
                    'target_count' => 2,
                    'reward_points' => 35,
                    'is_active' => 1,
                    'period' => 'weekly',
                    'icon' => 'svg-status',
                ],
            ];

            foreach ($quests as $quest) {
                DB::table('quests')->updateOrInsert(
                    ['slug' => $quest['slug']],
                    array_merge($quest, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('badges')) {
            $badgeSlugs = [
                'video-star',
                'clips-master',
                'audio-maestro',
                'resource-vault',
                'multimedia-pioneer',
            ];
            DB::table('badges')->whereIn('slug', $badgeSlugs)->delete();
        }

        if (Schema::hasTable('quests')) {
            $questSlugs = [
                'daily-video-creator',
                'daily-clips-creator',
                'weekly-multimedia-producer',
                'weekly-audio-sharer',
                'weekly-file-publisher',
            ];
            DB::table('quests')->whereIn('slug', $questSlugs)->delete();
        }
    }
};
