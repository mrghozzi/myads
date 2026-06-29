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
        $categories = [
            'graphics' => ['logos', 'vectors', 'templates', 'ui_kits'],
            'audio' => ['music', 'sound_effects', 'loops'],
            'video' => ['templates', 'stock_footage', 'motion_graphics'],
            'ebooks' => ['fiction', 'non_fiction', 'tutorials', 'guides'],
            'software' => ['apps', 'games', 'tools'],
            'courses' => ['programming', 'design', 'marketing'],
        ];

        foreach ($categories as $cat => $subcats) {
            if (!DB::table('options')->where('o_type', 'storecat')->where('name', $cat)->exists()) {
                DB::table('options')->insert([
                    'name' => $cat,
                    'o_valuer' => '0',
                    'o_type' => 'storecat',
                    'o_parent' => 0,
                    'o_order' => 0,
                    'o_mode' => $cat
                ]);
            }

            foreach ($subcats as $idx => $subcat) {
                if (!DB::table('options')->where('o_type', $cat . 'cat')->where('name', $subcat)->exists()) {
                    DB::table('options')->insert([
                        'name' => $subcat,
                        'o_valuer' => '0',
                        'o_type' => $cat . 'cat',
                        'o_parent' => 0,
                        'o_order' => $idx,
                        'o_mode' => $subcat
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $categories = ['graphics', 'audio', 'video', 'ebooks', 'software', 'courses'];

        foreach ($categories as $cat) {
            DB::table('options')->where('o_type', 'storecat')->where('name', $cat)->delete();
            DB::table('options')->where('o_type', $cat . 'cat')->delete();
        }
    }
};
