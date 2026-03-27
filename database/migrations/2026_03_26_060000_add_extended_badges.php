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
                'slug' => 'night-owl',
                'name_key' => 'badge_night_owl_name',
                'description_key' => 'badge_night_owl_desc',
                'icon' => 'fa-moon',
                'color' => 'indigo',
                'points_reward' => 50,
                'criteria_type' => 'night_posts',
                'criteria_target' => 5,
                'sort_order' => 10
            ],
            [
                'slug' => 'social-butterfly',
                'name_key' => 'badge_social_butterfly_name',
                'description_key' => 'badge_social_butterfly_desc',
                'icon' => 'fa-users',
                'color' => 'pink',
                'points_reward' => 100,
                'criteria_type' => 'following_count',
                'criteria_target' => 50,
                'sort_order' => 11
            ],
            [
                'slug' => 'marketplace-collector',
                'name_key' => 'badge_marketplace_collector_name',
                'description_key' => 'badge_marketplace_collector_desc',
                'icon' => 'fa-store',
                'color' => 'green',
                'points_reward' => 150,
                'criteria_type' => 'product_count',
                'criteria_target' => 5,
                'sort_order' => 12
            ],
            [
                'slug' => 'directory-guru',
                'name_key' => 'badge_directory_guru_name',
                'description_key' => 'badge_directory_guru_desc',
                'icon' => 'fa-atlas',
                'color' => 'blue',
                'points_reward' => 100,
                'criteria_type' => 'directory_count',
                'criteria_target' => 10,
                'sort_order' => 13
            ],
            [
                'slug' => 'active-surfer',
                'name_key' => 'badge_active_surfer_name',
                'description_key' => 'badge_active_surfer_desc',
                'icon' => 'fa-water',
                'color' => 'cyan',
                'points_reward' => 200,
                'criteria_type' => 'visit_exchanges',
                'criteria_target' => 100,
                'sort_order' => 14
            ],
            [
                'slug' => 'point-millionaire',
                'name_key' => 'badge_point_millionaire_name',
                'description_key' => 'badge_point_millionaire_desc',
                'icon' => 'fa-gem',
                'color' => 'gold',
                'points_reward' => 500,
                'criteria_type' => 'points_balance',
                'criteria_target' => 1000000,
                'sort_order' => 15
            ],
            [
                'slug' => 'ad-campaigner',
                'name_key' => 'badge_ad_campaigner_name',
                'description_key' => 'badge_ad_campaigner_desc',
                'icon' => 'fa-ad',
                'color' => 'orange',
                'points_reward' => 100,
                'criteria_type' => 'ads_count',
                'criteria_target' => 5,
                'sort_order' => 16
            ],
            [
                'slug' => 'knowledge-contributor',
                'name_key' => 'badge_knowledge_contributor_name',
                'description_key' => 'badge_knowledge_contributor_desc',
                'icon' => 'fa-book-open',
                'color' => 'teal',
                'points_reward' => 150,
                'criteria_type' => 'kb_articles',
                'criteria_target' => 3,
                'sort_order' => 17
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
        $slugs = [
            'night-owl', 'social-butterfly', 'marketplace-collector', 'directory-guru',
            'active-surfer', 'point-millionaire', 'ad-campaigner', 'knowledge-contributor'
        ];
        DB::table('badges')->whereIn('slug', $slugs)->delete();
    }
};
