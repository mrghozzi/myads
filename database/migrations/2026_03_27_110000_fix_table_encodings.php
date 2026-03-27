<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to fix encoding issues for Arabic content.
 * Specifically targets tables that store user-generated text.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Convert problematic tables to utf8mb4 to support Arabic and other multi-byte characters
            DB::statement('ALTER TABLE status CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            DB::statement('ALTER TABLE order_requests CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            DB::statement('ALTER TABLE options CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            
            // Also ensure forum and comments are covered just in case
            DB::statement('ALTER TABLE forum CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            DB::statement('ALTER TABLE f_coment CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down(): void
    {
        // Reverting character sets is risky and usually unnecessary
    }
};
