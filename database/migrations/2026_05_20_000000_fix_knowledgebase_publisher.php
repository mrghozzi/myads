<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('options')
            ->where('o_type', 'knowledgebase')
            ->where('o_parent', 0)
            ->update(['o_parent' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data migration, reversing is not strictly possible or required.
    }
};
