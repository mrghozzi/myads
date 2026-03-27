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
                'slug' => 'order-pioneer',
                'name_key' => 'badge_order_pioneer_name',
                'description_key' => 'badge_order_pioneer_desc',
                'icon' => 'fa-clipboard-check',
                'color' => 'blue',
                'points_reward' => 50,
                'criteria_type' => 'order_requests_count',
                'criteria_target' => 1,
                'sort_order' => 20
            ],
            [
                'slug' => 'best-helper',
                'name_key' => 'badge_best_helper_name',
                'description_key' => 'badge_best_helper_desc',
                'icon' => 'fa-award',
                'color' => 'gold',
                'points_reward' => 200,
                'criteria_type' => 'best_offers_won',
                'criteria_target' => 1,
                'sort_order' => 21
            ],
            [
                'slug' => 'top-bidder',
                'name_key' => 'badge_top_bidder_name',
                'description_key' => 'badge_top_bidder_desc',
                'icon' => 'fa-hand-holding-usd',
                'color' => 'green',
                'points_reward' => 100,
                'criteria_type' => 'order_bids_count',
                'criteria_target' => 10,
                'sort_order' => 22
            ],
            [
                'slug' => 'highly-rated',
                'name_key' => 'badge_highly_rated_name',
                'description_key' => 'badge_highly_rated_desc',
                'icon' => 'fa-star',
                'color' => 'orange',
                'points_reward' => 150,
                'criteria_type' => 'five_star_ratings',
                'criteria_target' => 1,
                'sort_order' => 23
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
        $slugs = ['order-pioneer', 'best-helper', 'top-bidder', 'highly-rated'];
        DB::table('badges')->whereIn('slug', $slugs)->delete();
    }
};
