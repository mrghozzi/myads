<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('groups')) {
            Schema::table('groups', function (Blueprint $table) {
                if (!Schema::hasColumn('groups', 'category')) {
                    $table->string('category', 64)->default('general')->after('name');
                    $table->index(['status', 'category']);
                }

                if (!Schema::hasColumn('groups', 'posting_permission')) {
                    $table->string('posting_permission', 24)->default('all_members')->after('privacy');
                }

                if (!Schema::hasColumn('groups', 'join_fee_pts')) {
                    $table->unsignedInteger('join_fee_pts')->default(0)->after('posting_permission');
                }

                if (!Schema::hasColumn('groups', 'pinned_post_id')) {
                    $table->unsignedBigInteger('pinned_post_id')->nullable()->after('posts_count');
                }
            });
        }

        if (Schema::hasTable('group_memberships')) {
            Schema::table('group_memberships', function (Blueprint $table) {
                if (!Schema::hasColumn('group_memberships', 'banned_at')) {
                    $table->timestamp('banned_at')->nullable()->after('rejected_by');
                }

                if (!Schema::hasColumn('group_memberships', 'banned_by')) {
                    $table->unsignedBigInteger('banned_by')->nullable()->after('banned_at');
                }

                // High speed compound index
                $table->index(['group_id', 'status', 'role'], 'gm_group_status_role_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('groups')) {
            Schema::table('groups', function (Blueprint $table) {
                if (Schema::hasColumn('groups', 'category')) {
                    $table->dropIndex(['status', 'category']);
                    $table->dropColumn('category');
                }
                if (Schema::hasColumn('groups', 'posting_permission')) {
                    $table->dropColumn('posting_permission');
                }
                if (Schema::hasColumn('groups', 'join_fee_pts')) {
                    $table->dropColumn('join_fee_pts');
                }
                if (Schema::hasColumn('groups', 'pinned_post_id')) {
                    $table->dropColumn('pinned_post_id');
                }
            });
        }

        if (Schema::hasTable('group_memberships')) {
            Schema::table('group_memberships', function (Blueprint $table) {
                $table->dropIndex('gm_group_status_role_idx');
                if (Schema::hasColumn('group_memberships', 'banned_at')) {
                    $table->dropColumn('banned_at');
                }
                if (Schema::hasColumn('group_memberships', 'banned_by')) {
                    $table->dropColumn('banned_by');
                }
            });
        }
    }
};

