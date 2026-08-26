<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('custom_ad_events') && !Schema::hasColumn('custom_ad_events', 'flag_reason')) {
            Schema::table('custom_ad_events', function (Blueprint $table) {
                $table->string('flag_reason', 48)->nullable()->after('is_flagged');
                $table->index(['event_type', 'is_flagged', 'flag_reason'], 'custom_ad_events_flag_reason_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('custom_ad_events') && Schema::hasColumn('custom_ad_events', 'flag_reason')) {
            Schema::table('custom_ad_events', function (Blueprint $table) {
                $table->dropIndex('custom_ad_events_flag_reason_idx');
                $table->dropColumn('flag_reason');
            });
        }
    }
};
