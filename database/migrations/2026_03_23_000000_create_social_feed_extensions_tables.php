<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('status_reposts')) {
            Schema::create('status_reposts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('status_id')->unique();
                $table->unsignedBigInteger('original_status_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->index('original_status_id');
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('status_link_previews')) {
            Schema::create('status_link_previews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('status_id')->unique();
                $table->string('url', 2048);
                $table->string('normalized_url', 2048)->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('image_url', 2048)->nullable();
                $table->string('site_name')->nullable();
                $table->string('domain')->nullable();
                $table->unsignedBigInteger('directory_id')->nullable();
                $table->unsignedBigInteger('directory_status_id')->nullable();
                $table->timestamps();

                $table->index('directory_id');
                $table->index('directory_status_id');
            });
        }

        if (!Schema::hasTable('status_mentions')) {
            Schema::create('status_mentions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('mentioned_user_id');
                $table->unsignedBigInteger('status_id')->nullable();
                $table->string('comment_type')->nullable();
                $table->unsignedBigInteger('comment_id')->nullable();
                $table->string('username');
                $table->timestamps();

                $table->index('mentioned_user_id');
                $table->index('status_id');
                $table->index(['comment_type', 'comment_id']);
            });
        }

        if (!Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 12, 2);
                $table->decimal('balance_after', 12, 2)->nullable();
                $table->string('type');
                $table->string('description_key')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index(['reference_type', 'reference_id']);
                $table->index('type');
            });
        }

        if (!Schema::hasTable('badges')) {
            Schema::create('badges', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name_key');
                $table->string('description_key');
                $table->string('icon')->nullable();
                $table->string('color')->nullable();
                $table->integer('points_reward')->default(0);
                $table->string('criteria_type');
                $table->unsignedInteger('criteria_target')->default(1);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('badge_id');
                $table->unsignedInteger('progress')->default(0);
                $table->timestamp('unlocked_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'badge_id']);
                $table->index('badge_id');
            });
        }

        if (!Schema::hasTable('badge_showcase')) {
            Schema::create('badge_showcase', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('badge_id');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['user_id', 'badge_id']);
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('quests')) {
            Schema::create('quests', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('period');
                $table->string('name_key');
                $table->string('description_key');
                $table->string('event_key');
                $table->unsignedInteger('target_count')->default(1);
                $table->integer('reward_points')->default(0);
                $table->string('icon')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('quest_progress')) {
            Schema::create('quest_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('quest_id');
                $table->string('period_key');
                $table->unsignedInteger('progress')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('rewarded_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'quest_id', 'period_key']);
                $table->index('quest_id');
            });
        }

        if (!Schema::hasTable('user_privacy_settings')) {
            Schema::create('user_privacy_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('profile_visibility')->default('public');
                $table->string('about_visibility')->default('public');
                $table->string('photos_visibility')->default('public');
                $table->string('followers_visibility')->default('public');
                $table->string('following_visibility')->default('public');
                $table->string('points_history_visibility')->default('private');
                $table->boolean('allow_direct_messages')->default(true);
                $table->boolean('allow_mentions')->default(true);
                $table->boolean('allow_reposts')->default(true);
                $table->boolean('show_online_status')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('site_admins')) {
            Schema::create('site_admins', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->boolean('is_super')->default(false);
                $table->boolean('has_full_access')->default(false);
                $table->json('permissions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('created_by');
            });
        }

        $this->migrateLegacyPointHistory();
        $this->seedBadges();
        $this->seedQuests();
        $this->seedDefaultPrivacyRows();
        $this->seedSuperAdmin();
    }

    public function down(): void
    {
        Schema::dropIfExists('site_admins');
        Schema::dropIfExists('user_privacy_settings');
        Schema::dropIfExists('quest_progress');
        Schema::dropIfExists('quests');
        Schema::dropIfExists('badge_showcase');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('status_mentions');
        Schema::dropIfExists('status_link_previews');
        Schema::dropIfExists('status_reposts');
    }

    private function migrateLegacyPointHistory(): void
    {
        if (!Schema::hasTable('options') || !Schema::hasTable('point_transactions')) {
            return;
        }

        $legacyRows = DB::table('options')
            ->where('o_type', 'hest_pts')
            ->orderBy('id')
            ->get();

        foreach ($legacyRows as $row) {
            $exists = DB::table('point_transactions')
                ->where('reference_type', 'legacy_option')
                ->where('reference_id', $row->id)
                ->exists();

            if ($exists) {
                continue;
            }

            $amount = is_numeric($row->o_valuer) ? (float) $row->o_valuer : 0.0;
            $createdAt = is_numeric($row->o_mode)
                ? date('Y-m-d H:i:s', (int) $row->o_mode)
                : now()->toDateTimeString();

            DB::table('point_transactions')->insert([
                'user_id' => (int) $row->o_parent,
                'amount' => $amount,
                'balance_after' => null,
                'type' => 'legacy',
                'description_key' => (string) ($row->name ?: 'legacy_points'),
                'reference_type' => 'legacy_option',
                'reference_id' => (int) $row->id,
                'meta' => json_encode([
                    'legacy_order' => (int) $row->o_order,
                    'legacy_mode' => $row->o_mode,
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    private function seedBadges(): void
    {
        if (!Schema::hasTable('badges')) {
            return;
        }

        $badges = [
            ['slug' => 'complete_profile', 'name_key' => 'badge_complete_profile_name', 'description_key' => 'badge_complete_profile_desc', 'icon' => 'badge', 'color' => 'violet', 'points_reward' => 20, 'criteria_type' => 'complete_profile', 'criteria_target' => 1, 'sort_order' => 1],
            ['slug' => 'first_post', 'name_key' => 'badge_first_post_name', 'description_key' => 'badge_first_post_desc', 'icon' => 'blog-posts', 'color' => 'cyan', 'points_reward' => 10, 'criteria_type' => 'post_count', 'criteria_target' => 1, 'sort_order' => 2],
            ['slug' => 'image_storyteller', 'name_key' => 'badge_image_storyteller_name', 'description_key' => 'badge_image_storyteller_desc', 'icon' => 'photos', 'color' => 'pink', 'points_reward' => 25, 'criteria_type' => 'image_post_count', 'criteria_target' => 10, 'sort_order' => 3],
            ['slug' => 'link_curator', 'name_key' => 'badge_link_curator_name', 'description_key' => 'badge_link_curator_desc', 'icon' => 'public', 'color' => 'blue', 'points_reward' => 25, 'criteria_type' => 'link_preview_count', 'criteria_target' => 5, 'sort_order' => 4],
            ['slug' => 'conversation_starter', 'name_key' => 'badge_conversation_starter_name', 'description_key' => 'badge_conversation_starter_desc', 'icon' => 'comment', 'color' => 'green', 'points_reward' => 30, 'criteria_type' => 'comment_count', 'criteria_target' => 25, 'sort_order' => 5],
            ['slug' => 'signal_booster', 'name_key' => 'badge_signal_booster_name', 'description_key' => 'badge_signal_booster_desc', 'icon' => 'share', 'color' => 'orange', 'points_reward' => 30, 'criteria_type' => 'repost_count', 'criteria_target' => 10, 'sort_order' => 6],
            ['slug' => 'crowd_favorite', 'name_key' => 'badge_crowd_favorite_name', 'description_key' => 'badge_crowd_favorite_desc', 'icon' => 'thumbs-up', 'color' => 'red', 'points_reward' => 35, 'criteria_type' => 'reactions_received', 'criteria_target' => 50, 'sort_order' => 7],
            ['slug' => 'community_magnet', 'name_key' => 'badge_community_magnet_name', 'description_key' => 'badge_community_magnet_desc', 'icon' => 'friend', 'color' => 'yellow', 'points_reward' => 40, 'criteria_type' => 'followers_count', 'criteria_target' => 25, 'sort_order' => 8],
        ];

        foreach ($badges as $badge) {
            DB::table('badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                array_merge($badge, [
                    'is_active' => true,
                    'meta' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    private function seedQuests(): void
    {
        if (!Schema::hasTable('quests')) {
            return;
        }

        $quests = [
            ['slug' => 'daily_first_post', 'period' => 'daily', 'name_key' => 'quest_daily_first_post_name', 'description_key' => 'quest_daily_first_post_desc', 'event_key' => 'post_created', 'target_count' => 1, 'reward_points' => 10, 'icon' => 'blog-posts', 'sort_order' => 1],
            ['slug' => 'daily_three_comments', 'period' => 'daily', 'name_key' => 'quest_daily_three_comments_name', 'description_key' => 'quest_daily_three_comments_desc', 'event_key' => 'comment_created', 'target_count' => 3, 'reward_points' => 12, 'icon' => 'comment', 'sort_order' => 2],
            ['slug' => 'daily_five_reactions_given', 'period' => 'daily', 'name_key' => 'quest_daily_five_reactions_given_name', 'description_key' => 'quest_daily_five_reactions_given_desc', 'event_key' => 'reaction_given', 'target_count' => 5, 'reward_points' => 10, 'icon' => 'thumbs-up', 'sort_order' => 3],
            ['slug' => 'daily_five_visit_exchanges', 'period' => 'daily', 'name_key' => 'quest_daily_five_visit_exchanges_name', 'description_key' => 'quest_daily_five_visit_exchanges_desc', 'event_key' => 'visit_exchange_completed', 'target_count' => 5, 'reward_points' => 20, 'icon' => 'overview', 'sort_order' => 4],
            ['slug' => 'weekly_three_reposts', 'period' => 'weekly', 'name_key' => 'quest_weekly_three_reposts_name', 'description_key' => 'quest_weekly_three_reposts_desc', 'event_key' => 'repost_created', 'target_count' => 3, 'reward_points' => 25, 'icon' => 'share', 'sort_order' => 5],
            ['slug' => 'weekly_ten_reactions_received', 'period' => 'weekly', 'name_key' => 'quest_weekly_ten_reactions_received_name', 'description_key' => 'quest_weekly_ten_reactions_received_desc', 'event_key' => 'reaction_received', 'target_count' => 10, 'reward_points' => 25, 'icon' => 'thumbs-up', 'sort_order' => 6],
        ];

        foreach ($quests as $quest) {
            DB::table('quests')->updateOrInsert(
                ['slug' => $quest['slug']],
                array_merge($quest, [
                    'is_active' => true,
                    'meta' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    private function seedDefaultPrivacyRows(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('user_privacy_settings')) {
            return;
        }

        $userIds = DB::table('users')->pluck('id');
        foreach ($userIds as $userId) {
            DB::table('user_privacy_settings')->updateOrInsert(
                ['user_id' => (int) $userId],
                [
                    'profile_visibility' => 'public',
                    'about_visibility' => 'public',
                    'photos_visibility' => 'public',
                    'followers_visibility' => 'public',
                    'following_visibility' => 'public',
                    'points_history_visibility' => 'private',
                    'allow_direct_messages' => true,
                    'allow_mentions' => true,
                    'allow_reposts' => true,
                    'show_online_status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function seedSuperAdmin(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('site_admins')) {
            return;
        }

        if (!DB::table('users')->where('id', 1)->exists()) {
            return;
        }

        DB::table('site_admins')->updateOrInsert(
            ['user_id' => 1],
            [
                'is_super' => true,
                'has_full_access' => true,
                'permissions' => json_encode([], JSON_UNESCAPED_UNICODE),
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
};
