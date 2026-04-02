<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('options')) {
            return;
        }

        $exists = DB::table('options')
            ->where('o_type', 'storecat')
            ->where('name', 'themes')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('options')->insert([
            'name' => 'themes',
            'o_valuer' => '0',
            'o_type' => 'storecat',
            'o_parent' => 0,
            'o_order' => 0,
            'o_mode' => 'themes',
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('options')) {
            return;
        }

        DB::table('options')
            ->where('o_type', 'storecat')
            ->where('name', 'themes')
            ->delete();
    }
};
