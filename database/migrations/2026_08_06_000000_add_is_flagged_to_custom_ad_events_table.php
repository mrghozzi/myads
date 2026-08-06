<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('custom_ad_events') && !Schema::hasColumn('custom_ad_events', 'is_flagged')) {
            Schema::table('custom_ad_events', function (Blueprint $table) {
                $table->boolean('is_flagged')->default(false)->after('user_agent');
                $table->index(['creative_id', 'is_flagged'], 'custom_ad_events_creative_flagged_idx');
                $table->index(['deal_id', 'is_flagged'], 'custom_ad_events_deal_flagged_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('custom_ad_events') && Schema::hasColumn('custom_ad_events', 'is_flagged')) {
            Schema::table('custom_ad_events', function (Blueprint $table) {
                $table->dropIndex('custom_ad_events_creative_flagged_idx');
                $table->dropIndex('custom_ad_events_deal_flagged_idx');
                $table->dropColumn('is_flagged');
            });
        }
    }
};
