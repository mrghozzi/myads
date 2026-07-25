<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('like')) {
            $this->addIndexIfMissing('like', 'like_uid_sid_type_idx', 'ALTER TABLE `like` ADD INDEX `like_uid_sid_type_idx` (`uid`, `sid`, `type`)');
            $this->addIndexIfMissing('like', 'like_sid_type_idx', 'ALTER TABLE `like` ADD INDEX `like_sid_type_idx` (`sid`, `type`)');
            $this->addIndexIfMissing('like', 'like_uid_type_idx', 'ALTER TABLE `like` ADD INDEX `like_uid_type_idx` (`uid`, `type`)');
        }

        if (Schema::hasTable('options')) {
            $this->addIndexIfMissing('options', 'options_parent_type_idx', 'ALTER TABLE `options` ADD INDEX `options_parent_type_idx` (`o_parent`, `o_type`(64))');
            $this->addIndexIfMissing('options', 'options_order_type_idx', 'ALTER TABLE `options` ADD INDEX `options_order_type_idx` (`o_order`, `o_type`(64))');
        }

        if (Schema::hasTable('notif')) {
            $this->addIndexIfMissing('notif', 'notif_uid_time_state_idx', 'ALTER TABLE `notif` ADD INDEX `notif_uid_time_state_idx` (`uid`, `time`, `state`)');
        }
    }

    private function addIndexIfMissing(string $table, string $indexName, string $sql): void
    {
        try {
            $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (empty($exists)) {
                DB::statement($sql);
            }
        } catch (\Throwable $e) {
            // Ignore if already exists or fails gracefully
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('like')) {
            Schema::table('like', function (Blueprint $table) {
                $table->dropIndex('like_uid_sid_type_idx');
                $table->dropIndex('like_sid_type_idx');
                $table->dropIndex('like_uid_type_idx');
            });
        }

        if (Schema::hasTable('options')) {
            Schema::table('options', function (Blueprint $table) {
                $table->dropIndex('options_parent_type_idx');
                $table->dropIndex('options_order_type_idx');
            });
        }

        if (Schema::hasTable('notif')) {
            Schema::table('notif', function (Blueprint $table) {
                $table->dropIndex('notif_uid_time_state_idx');
            });
        }
    }
};
