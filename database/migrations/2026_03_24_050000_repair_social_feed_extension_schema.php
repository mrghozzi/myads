<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $migration = require base_path('database/migrations/2026_03_23_000000_create_social_feed_extensions_tables.php');
        $migration->up();
    }

    public function down(): void
    {
        // Repair-only migration; intentionally irreversible.
    }
};
