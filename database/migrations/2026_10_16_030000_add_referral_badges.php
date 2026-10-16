<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('badges')) {
            return;
        }

        $badges = [
            [
                'slug' => 'referral_rookie',
                'name_key' => 'badge_referral_rookie_name',
                'description_key' => 'badge_referral_rookie_desc',
                'icon' => 'fa-user-plus',
                'color' => 'blue',
                'points_reward' => 50,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 1,
                'sort_order' => 34,
                'is_active' => true,
            ],
            [
                'slug' => 'network_builder',
                'name_key' => 'badge_network_builder_name',
                'description_key' => 'badge_network_builder_desc',
                'icon' => 'fa-share-nodes',
                'color' => 'teal',
                'points_reward' => 150,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 5,
                'sort_order' => 35,
                'is_active' => true,
            ],
            [
                'slug' => 'super_promoter',
                'name_key' => 'badge_super_promoter_name',
                'description_key' => 'badge_super_promoter_desc',
                'icon' => 'fa-bullhorn',
                'color' => 'purple',
                'points_reward' => 500,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 20,
                'sort_order' => 36,
                'is_active' => true,
            ],
            [
                'slug' => 'referral_legend',
                'name_key' => 'badge_referral_legend_name',
                'description_key' => 'badge_referral_legend_desc',
                'icon' => 'fa-crown',
                'color' => 'gold',
                'points_reward' => 1500,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 50,
                'sort_order' => 37,
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('badges')) {
            return;
        }

        $slugs = ['referral_rookie', 'network_builder', 'super_promoter', 'referral_legend'];
        DB::table('badges')->whereIn('slug', $slugs)->delete();
    }
};
