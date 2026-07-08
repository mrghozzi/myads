<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('status', function (Blueprint $table) {
            if (!Schema::hasColumn('status', 'is_pinned')) {
                $table->boolean('is_pinned')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status', function (Blueprint $table) {
            if (Schema::hasColumn('status', 'is_pinned')) {
                $table->dropColumn('is_pinned');
            }
        });
    }
};
