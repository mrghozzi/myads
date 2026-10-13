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
        Schema::table('f_coment', function (Blueprint $table) {
            if (!Schema::hasColumn('f_coment', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->index();
            }
            if (!Schema::hasColumn('f_coment', 'is_best_answer')) {
                $table->boolean('is_best_answer')->default(false)->index();
            }
        });

        Schema::table('forum', function (Blueprint $table) {
            if (!Schema::hasColumn('forum', 'best_comment_id')) {
                $table->unsignedBigInteger('best_comment_id')->nullable()->index();
            }
            if (!Schema::hasColumn('forum', 'pinned_comment_id')) {
                $table->unsignedBigInteger('pinned_comment_id')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('f_coment', function (Blueprint $table) {
            if (Schema::hasColumn('f_coment', 'is_pinned')) {
                $table->dropColumn('is_pinned');
            }
            if (Schema::hasColumn('f_coment', 'is_best_answer')) {
                $table->dropColumn('is_best_answer');
            }
        });

        Schema::table('forum', function (Blueprint $table) {
            if (Schema::hasColumn('forum', 'best_comment_id')) {
                $table->dropColumn('best_comment_id');
            }
            if (Schema::hasColumn('forum', 'pinned_comment_id')) {
                $table->dropColumn('pinned_comment_id');
            }
        });
    }
};
