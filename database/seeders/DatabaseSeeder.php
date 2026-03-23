<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with essential default data.
     */
    public function run(): void
    {
        // Default site settings
        if (Schema::hasTable('setting') && DB::table('setting')->count() === 0) {
            DB::table('setting')->insert([
                'titer' => 'MyAds',
                'description' => 'Your Advertising Platform',
                'url' => config('app.url', 'http://localhost'),
                'styles' => 'default',
                'lang' => 'en',
                'timezone' => 'UTC',
                'close' => 1,
                'close_text' => '',
                'a_mail' => '',
                'a_not' => '',
                'e_links' => 1,
                'facebook' => '#',
                'twitter' => '#',
                'linkedin' => '#',
            ]);
        }

        // Default site ads placeholders
        if (Schema::hasTable('ads') && DB::table('ads')->count() === 0) {
            for ($i = 1; $i <= 6; $i++) {
                DB::table('ads')->insert([
                    'code_ads' => '<!-- MyAds ad slot ' . $i . ' -->',
                ]);
            }
        }

        // Default store categories
        if (Schema::hasTable('options')) {
            $storeCategories = [
                ['name' => 'script', 'o_valuer' => '0', 'o_type' => 'storecat', 'o_parent' => 0, 'o_order' => 0, 'o_mode' => 'script'],
                ['name' => 'plugins', 'o_valuer' => '0', 'o_type' => 'storecat', 'o_parent' => 0, 'o_order' => 0, 'o_mode' => 'plugins'],
                ['name' => 'templates', 'o_valuer' => '0', 'o_type' => 'storecat', 'o_parent' => 0, 'o_order' => 0, 'o_mode' => 'templates'],
            ];

            foreach ($storeCategories as $cat) {
                if (!DB::table('options')->where('o_type', 'storecat')->where('name', $cat['name'])->exists()) {
                    DB::table('options')->insert($cat);
                }
            }

            // Default languages
            $languages = [
                ['name' => 'English', 'o_valuer' => 'en', 'o_type' => 'languages', 'o_parent' => 0, 'o_order' => 0, 'o_mode' => '0'],
                ['name' => 'Arabic', 'o_valuer' => 'ar', 'o_type' => 'languages', 'o_parent' => 0, 'o_order' => 0, 'o_mode' => '0'],
            ];

            foreach ($languages as $lang) {
                if (!DB::table('options')->where('o_type', 'languages')->where('o_valuer', $lang['o_valuer'])->exists()) {
                    DB::table('options')->insert($lang);
                }
            }

            // Default version
            if (!DB::table('options')->where('o_type', 'version')->exists()) {
                DB::table('options')->insert([
                    'name' => 'version',
                    'o_valuer' => '4.1.3',
                    'o_type' => 'version',
                    'o_parent' => 0,
                    'o_order' => 0,
                    'o_mode' => '0',
                ]);
            }
        }
    }
}
