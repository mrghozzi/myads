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
        // 1. Add push columns to user_notification_settings
        if (Schema::hasTable('user_notification_settings')) {
            Schema::table('user_notification_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('user_notification_settings', 'push_new_follower')) {
                    $table->boolean('push_new_follower')->default(true)->after('email_marketplace_update');
                }
                if (!Schema::hasColumn('user_notification_settings', 'push_new_comment')) {
                    $table->boolean('push_new_comment')->default(true)->after('push_new_follower');
                }
                if (!Schema::hasColumn('user_notification_settings', 'push_new_message')) {
                    $table->boolean('push_new_message')->default(true)->after('push_new_comment');
                }
                if (!Schema::hasColumn('user_notification_settings', 'push_mention')) {
                    $table->boolean('push_mention')->default(true)->after('push_new_message');
                }
                if (!Schema::hasColumn('user_notification_settings', 'push_reaction')) {
                    $table->boolean('push_reaction')->default(true)->after('push_mention');
                }
                if (!Schema::hasColumn('user_notification_settings', 'push_forum_reply')) {
                    $table->boolean('push_forum_reply')->default(true)->after('push_reaction');
                }
            });
        }

        // 2. Add sender_id column to notif table
        if (Schema::hasTable('notif')) {
            Schema::table('notif', function (Blueprint $table) {
                if (!Schema::hasColumn('notif', 'sender_id')) {
                    $table->unsignedBigInteger('sender_id')->nullable()->after('uid');
                    $table->index('sender_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_notification_settings')) {
            Schema::table('user_notification_settings', function (Blueprint $table) {
                $columns = [
                    'push_new_follower',
                    'push_new_comment',
                    'push_new_message',
                    'push_mention',
                    'push_reaction',
                    'push_forum_reply',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('user_notification_settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('notif')) {
            Schema::table('notif', function (Blueprint $table) {
                if (Schema::hasColumn('notif', 'sender_id')) {
                    $table->dropColumn('sender_id');
                }
            });
        }
    }
};

