<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // 1. status table: (s_type, statu, date)
        if (Schema::hasTable('status')) {
            if ($isMysql) {
                try {
                    // Normalize table engine & format for long index keys
                    DB::statement('ALTER TABLE `status` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
                    
                    // Sanitize any corrupt non-numeric data in legacy columns before type conversion
                    try {
                        DB::statement("UPDATE `status` SET `s_type` = 100 WHERE `s_type` IS NULL OR `s_type` = ''");
                        DB::statement("UPDATE `status` SET `statu` = 1 WHERE `statu` IS NULL OR `statu` = ''");
                        DB::statement("UPDATE `status` SET `date` = 0 WHERE `date` IS NULL OR `date` = ''");
                    } catch (\Throwable $e) {}

                    DB::statement('ALTER TABLE `status` MODIFY `s_type` INT NOT NULL DEFAULT 100, MODIFY `statu` TINYINT NOT NULL DEFAULT 1, MODIFY `date` BIGINT NOT NULL DEFAULT 0');
                } catch (\Throwable $e) {
                    // Fallback if alter fails on specific hosting constraints
                }
            }

            if (!Schema::hasIndex('status', 'status_type_statu_date_idx')) {
                try {
                    Schema::table('status', function (Blueprint $table) {
                        $table->index(['s_type', 'statu', 'date'], 'status_type_statu_date_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not add status_type_statu_date_idx: ' . $e->getMessage());
                }
            }
        }

        // 2. smart_ads table: (statu, uid)
        if (Schema::hasTable('smart_ads')) {
            if ($isMysql) {
                try {
                    DB::statement('ALTER TABLE `smart_ads` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
                    DB::statement('ALTER TABLE `smart_ads` MODIFY `statu` TINYINT NOT NULL DEFAULT 1, MODIFY `uid` BIGINT UNSIGNED NOT NULL DEFAULT 0');
                } catch (\Throwable $e) {}
            }

            if (!Schema::hasIndex('smart_ads', 'smart_ads_statu_uid_idx')) {
                try {
                    Schema::table('smart_ads', function (Blueprint $table) {
                        $table->index(['statu', 'uid'], 'smart_ads_statu_uid_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not add smart_ads_statu_uid_idx: ' . $e->getMessage());
                }
            }
        }

        // 3. banner table: (statu, px)
        if (Schema::hasTable('banner')) {
            if ($isMysql) {
                try {
                    DB::statement('ALTER TABLE `banner` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
                    DB::statement('ALTER TABLE `banner` MODIFY `statu` TINYINT NOT NULL DEFAULT 1, MODIFY `px` VARCHAR(64) NOT NULL DEFAULT \'\'');
                } catch (\Throwable $e) {}
            }

            if (!Schema::hasIndex('banner', 'banner_statu_px_idx')) {
                try {
                    Schema::table('banner', function (Blueprint $table) {
                        $table->index(['statu', 'px'], 'banner_statu_px_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not add banner_statu_px_idx: ' . $e->getMessage());
                }
            }
        }

        // 4. link table: (statu, id)
        if (Schema::hasTable('link')) {
            if ($isMysql) {
                try {
                    DB::statement('ALTER TABLE `link` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
                    DB::statement('ALTER TABLE `link` MODIFY `statu` TINYINT NOT NULL DEFAULT 1');
                } catch (\Throwable $e) {}
            }

            if (!Schema::hasIndex('link', 'link_statu_id_idx')) {
                try {
                    Schema::table('link', function (Blueprint $table) {
                        $table->index(['statu', 'id'], 'link_statu_id_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not add link_statu_id_idx: ' . $e->getMessage());
                }
            }
        }

        // 5. custom_ad_events table: (deal_id, event_type, is_flagged, occurred_at)
        if (Schema::hasTable('custom_ad_events')) {
            if ($isMysql) {
                try {
                    DB::statement('ALTER TABLE `custom_ad_events` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
                    DB::statement('ALTER TABLE `custom_ad_events` MODIFY `event_type` VARCHAR(32) NOT NULL');
                } catch (\Throwable $e) {}
            }

            if (!Schema::hasIndex('custom_ad_events', 'custom_ad_events_deal_eval_idx')) {
                try {
                    Schema::table('custom_ad_events', function (Blueprint $table) {
                        $table->index(['deal_id', 'event_type', 'is_flagged', 'occurred_at'], 'custom_ad_events_deal_eval_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not add custom_ad_events_deal_eval_idx: ' . $e->getMessage());
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('custom_ad_events') && Schema::hasIndex('custom_ad_events', 'custom_ad_events_deal_eval_idx')) {
            Schema::table('custom_ad_events', function (Blueprint $table) {
                $table->dropIndex('custom_ad_events_deal_eval_idx');
            });
        }

        if (Schema::hasTable('link') && Schema::hasIndex('link', 'link_statu_id_idx')) {
            Schema::table('link', function (Blueprint $table) {
                $table->dropIndex('link_statu_id_idx');
            });
        }

        if (Schema::hasTable('banner') && Schema::hasIndex('banner', 'banner_statu_px_idx')) {
            Schema::table('banner', function (Blueprint $table) {
                $table->dropIndex('banner_statu_px_idx');
            });
        }

        if (Schema::hasTable('smart_ads') && Schema::hasIndex('smart_ads', 'smart_ads_statu_uid_idx')) {
            Schema::table('smart_ads', function (Blueprint $table) {
                $table->dropIndex('smart_ads_statu_uid_idx');
            });
        }

        if (Schema::hasTable('status') && Schema::hasIndex('status', 'status_type_statu_date_idx')) {
            Schema::table('status', function (Blueprint $table) {
                $table->dropIndex('status_type_statu_date_idx');
            });
        }
    }
};
