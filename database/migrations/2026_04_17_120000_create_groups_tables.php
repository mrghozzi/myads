<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('owner_id');
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('short_description', 280)->nullable();
                $table->text('description')->nullable();
                $table->longText('rules_markdown')->nullable();
                $table->string('privacy', 24)->default('public');
                $table->string('status', 24)->default('active');
                $table->string('avatar_path')->nullable();
                $table->string('cover_path')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('members_count')->default(0);
                $table->unsignedInteger('posts_count')->default(0);
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'privacy']);
                $table->index(['is_featured', 'last_activity_at']);
                $table->index('owner_id');
            });
        }

        if (!Schema::hasTable('group_memberships')) {
            Schema::create('group_memberships', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->unsignedBigInteger('user_id');
                $table->string('role', 24)->default('member');
                $table->string('status', 24)->default('pending');
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->unsignedBigInteger('rejected_by')->nullable();
                $table->timestamps();

                $table->unique(['group_id', 'user_id']);
                $table->index(['group_id', 'status']);
                $table->index(['user_id', 'status']);
            });
        }

        if (Schema::hasTable('forum') && !Schema::hasColumn('forum', 'group_id')) {
            Schema::table('forum', function (Blueprint $table) {
                $table->unsignedBigInteger('group_id')->nullable()->after('cat');
                $table->index(['group_id', 'statu']);
            });
        }

        if (Schema::hasTable('status') && !Schema::hasColumn('status', 'group_id')) {
            Schema::table('status', function (Blueprint $table) {
                $table->unsignedBigInteger('group_id')->nullable()->after('tp_id');
                $table->index(['group_id', 'date']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('status') && Schema::hasColumn('status', 'group_id')) {
            Schema::table('status', function (Blueprint $table) {
                $table->dropIndex('status_group_id_date_index');
                $table->dropColumn('group_id');
            });
        }

        if (Schema::hasTable('forum') && Schema::hasColumn('forum', 'group_id')) {
            Schema::table('forum', function (Blueprint $table) {
                $table->dropIndex('forum_group_id_statu_index');
                $table->dropColumn('group_id');
            });
        }

        Schema::dropIfExists('group_memberships');
        Schema::dropIfExists('groups');
    }
};
