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
        // 1. Enhance the report table with moderation categorization and resolution metadata
        if (Schema::hasTable('report')) {
            Schema::table('report', function (Blueprint $table) {
                if (!Schema::hasColumn('report', 'category')) {
                    $table->string('category', 64)->nullable()->default('other')->after('txt');
                }
                if (!Schema::hasColumn('report', 'action_taken')) {
                    $table->string('action_taken', 64)->nullable()->default('none')->after('statu');
                }
                if (!Schema::hasColumn('report', 'action_notes')) {
                    $table->text('action_notes')->nullable()->after('action_taken');
                }
                if (!Schema::hasColumn('report', 'moderator_id')) {
                    $table->unsignedBigInteger('moderator_id')->nullable()->after('action_notes');
                }
                if (!Schema::hasColumn('report', 'resolved_at')) {
                    $table->unsignedInteger('resolved_at')->nullable()->after('moderator_id');
                }
            });
        }

        // 2. Create moderation_logs table for comprehensive audit trail
        if (!Schema::hasTable('moderation_logs')) {
            Schema::create('moderation_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('moderator_id')->nullable()->index();
                $table->string('target_type', 64)->index();
                $table->unsignedBigInteger('target_id')->index();
                $table->string('action', 64)->index(); // dismiss, hide, delete, warn_user, ban_user, auto_quarantine
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('report_id')->nullable()->index();
                $table->json('metadata')->nullable();
                $table->unsignedInteger('created_at')->nullable()->index();
            });
        }

        // 3. Create user_warnings table for progressive strikes and penalties
        if (!Schema::hasTable('user_warnings')) {
            Schema::create('user_warnings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('moderator_id')->nullable()->index();
                $table->string('reason', 255);
                $table->text('details')->nullable();
                $table->integer('points_deducted')->default(0);
                $table->unsignedTinyInteger('strike_level')->default(1);
                $table->unsignedInteger('created_at')->nullable()->index();
            });
        }

        // 4. Add parent_id to f_coment for threaded / nested comments
        if (Schema::hasTable('f_coment')) {
            Schema::table('f_coment', function (Blueprint $table) {
                if (!Schema::hasColumn('f_coment', 'parent_id')) {
                    $table->unsignedBigInteger('parent_id')->nullable()->index()->after('tid');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('f_coment') && Schema::hasColumn('f_coment', 'parent_id')) {
            Schema::table('f_coment', function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }

        Schema::dropIfExists('user_warnings');
        Schema::dropIfExists('moderation_logs');

        if (Schema::hasTable('report')) {
            Schema::table('report', function (Blueprint $table) {
                $columns = ['category', 'action_taken', 'action_notes', 'moderator_id', 'resolved_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('report', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
