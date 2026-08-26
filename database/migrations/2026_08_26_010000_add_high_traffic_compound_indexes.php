<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. status table: (s_type, statu, date)
        if (Schema::hasTable('status')) {
            Schema::table('status', function (Blueprint $table) {
                $table->index(['s_type', 'statu', 'date'], 'status_type_statu_date_idx');
            });
        }

        // 2. smart_ads table: (statu, uid)
        if (Schema::hasTable('smart_ads')) {
            Schema::table('smart_ads', function (Blueprint $table) {
                $table->index(['statu', 'uid'], 'smart_ads_statu_uid_idx');
            });
        }

        // 3. banner table: (statu, px)
        if (Schema::hasTable('banner')) {
            Schema::table('banner', function (Blueprint $table) {
                $table->index(['statu', 'px'], 'banner_statu_px_idx');
            });
        }

        // 4. link table: (statu, id)
        if (Schema::hasTable('link')) {
            Schema::table('link', function (Blueprint $table) {
                $table->index(['statu', 'id'], 'link_statu_id_idx');
            });
        }

        // 5. custom_ad_events table: (deal_id, event_type, is_flagged, occurred_at)
        if (Schema::hasTable('custom_ad_events')) {
            Schema::table('custom_ad_events', function (Blueprint $table) {
                $table->index(['deal_id', 'event_type', 'is_flagged', 'occurred_at'], 'custom_ad_events_deal_eval_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('custom_ad_events')) {
            Schema::table('custom_ad_events', function (Blueprint $table) {
                $table->dropIndex('custom_ad_events_deal_eval_idx');
            });
        }

        if (Schema::hasTable('link')) {
            Schema::table('link', function (Blueprint $table) {
                $table->dropIndex('link_statu_id_idx');
            });
        }

        if (Schema::hasTable('banner')) {
            Schema::table('banner', function (Blueprint $table) {
                $table->dropIndex('banner_statu_px_idx');
            });
        }

        if (Schema::hasTable('smart_ads')) {
            Schema::table('smart_ads', function (Blueprint $table) {
                $table->dropIndex('smart_ads_statu_uid_idx');
            });
        }

        if (Schema::hasTable('status')) {
            Schema::table('status', function (Blueprint $table) {
                $table->dropIndex('status_type_statu_date_idx');
            });
        }
    }
};
