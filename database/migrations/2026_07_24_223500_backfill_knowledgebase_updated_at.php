<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('options', 'updated_at')) {
            DB::table('options')
                ->where('o_type', 'knowledgebase')
                ->where(function ($query) {
                    $query->whereNull('updated_at')
                          ->orWhere('updated_at', '0000-00-00 00:00:00');
                })
                ->update(['updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // No-op reverse migration
    }
};
