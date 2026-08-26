<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('custom_ad_events')) {
            if (DB::getDriverName() === 'mysql') {
                try {
                    DB::statement('ALTER TABLE `custom_ad_events` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
                    DB::statement('ALTER TABLE `custom_ad_events` MODIFY `event_type` VARCHAR(32) NOT NULL');
                } catch (\Throwable $e) {}
            }

            if (!Schema::hasColumn('custom_ad_events', 'flag_reason')) {
                Schema::table('custom_ad_events', function (Blueprint $table) {
                    $table->string('flag_reason', 48)->nullable()->after('is_flagged');
                });
            }

            if (!Schema::hasIndex('custom_ad_events', 'custom_ad_events_flag_reason_idx')) {
                try {
                    Schema::table('custom_ad_events', function (Blueprint $table) {
                        $table->index(['event_type', 'is_flagged', 'flag_reason'], 'custom_ad_events_flag_reason_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not add custom_ad_events_flag_reason_idx: ' . $e->getMessage());
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('custom_ad_events')) {
            if (Schema::hasIndex('custom_ad_events', 'custom_ad_events_flag_reason_idx')) {
                Schema::table('custom_ad_events', function (Blueprint $table) {
                    $table->dropIndex('custom_ad_events_flag_reason_idx');
                });
            }

            if (Schema::hasColumn('custom_ad_events', 'flag_reason')) {
                Schema::table('custom_ad_events', function (Blueprint $table) {
                    $table->dropColumn('flag_reason');
                });
            }
        }
    }
};
