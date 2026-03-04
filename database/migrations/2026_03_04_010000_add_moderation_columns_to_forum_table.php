<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('forum')) {
            return;
        }

        Schema::table('forum', function (Blueprint $table) {
            if (!Schema::hasColumn('forum', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false);
            }
            if (!Schema::hasColumn('forum', 'pinned_at')) {
                $table->unsignedBigInteger('pinned_at')->nullable();
            }
            if (!Schema::hasColumn('forum', 'pinned_by')) {
                $table->unsignedBigInteger('pinned_by')->nullable();
            }
            if (!Schema::hasColumn('forum', 'is_locked')) {
                $table->boolean('is_locked')->default(false);
            }
            if (!Schema::hasColumn('forum', 'locked_at')) {
                $table->unsignedBigInteger('locked_at')->nullable();
            }
            if (!Schema::hasColumn('forum', 'locked_by')) {
                $table->unsignedBigInteger('locked_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('forum')) {
            return;
        }

        Schema::table('forum', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['is_pinned', 'pinned_at', 'pinned_by', 'is_locked', 'locked_at', 'locked_by'] as $column) {
                if (Schema::hasColumn('forum', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
