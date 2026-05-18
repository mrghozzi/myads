<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('custom_ad_placements')) {
            Schema::create('custom_ad_placements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->string('placement_key', 48)->unique();
                $table->string('format', 24)->default('banner');
                $table->string('size', 24)->default('responsive');
                $table->string('site_url')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(true);
                $table->string('status', 24)->default('active');
                $table->string('background_color', 16)->default('#ffffff');
                $table->string('text_color', 16)->default('#1f2937');
                $table->string('accent_color', 16)->default('#615dfa');
                $table->unsignedInteger('impressions')->default(0);
                $table->unsignedInteger('clicks')->default(0);
                $table->timestamps();

                $table->index(['user_id', 'status'], 'custom_ad_placements_owner_status_idx');
                $table->index(['is_public', 'status'], 'custom_ad_placements_market_idx');
            });
        }

        if (!Schema::hasTable('custom_ad_deals')) {
            Schema::create('custom_ad_deals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('placement_id');
                $table->unsignedBigInteger('publisher_id');
                $table->unsignedBigInteger('advertiser_id');
                $table->unsignedBigInteger('initiated_by_id');
                $table->string('source', 24)->default('request');
                $table->string('payment_type', 24)->default('pts_daily');
                $table->string('status', 24)->default('pending');
                $table->decimal('daily_pts', 12, 2)->default(0);
                $table->decimal('total_pts', 12, 2)->default(0);
                $table->decimal('reserved_pts', 12, 2)->default(0);
                $table->decimal('paid_pts', 12, 2)->default(0);
                $table->decimal('refunded_pts', 12, 2)->default(0);
                $table->decimal('external_amount', 12, 2)->nullable();
                $table->string('external_currency', 8)->nullable();
                $table->text('external_note')->nullable();
                $table->text('terms')->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->date('last_paid_on')->nullable();
                $table->unsignedInteger('impressions')->default(0);
                $table->unsignedInteger('clicks')->default(0);
                $table->timestamps();

                $table->index(['placement_id', 'status'], 'custom_ad_deals_placement_status_idx');
                $table->index(['publisher_id', 'status'], 'custom_ad_deals_publisher_status_idx');
                $table->index(['advertiser_id', 'status'], 'custom_ad_deals_advertiser_status_idx');
                $table->index(['payment_type', 'status', 'ends_at'], 'custom_ad_deals_settlement_idx');
            });
        }

        if (!Schema::hasTable('custom_ad_creatives')) {
            Schema::create('custom_ad_creatives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('deal_id');
                $table->string('token', 64)->unique();
                $table->string('format', 24)->default('banner');
                $table->string('headline');
                $table->text('body')->nullable();
                $table->string('image_url')->nullable();
                $table->string('target_url', 2048);
                $table->string('button_label', 80)->nullable();
                $table->string('background_color', 16)->default('#ffffff');
                $table->string('text_color', 16)->default('#1f2937');
                $table->string('accent_color', 16)->default('#615dfa');
                $table->string('status', 24)->default('approved');
                $table->unsignedInteger('impressions')->default(0);
                $table->unsignedInteger('clicks')->default(0);
                $table->timestamps();

                $table->index(['deal_id', 'status'], 'custom_ad_creatives_deal_status_idx');
            });
        }

        if (!Schema::hasTable('custom_ad_events')) {
            Schema::create('custom_ad_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('placement_id');
                $table->unsignedBigInteger('deal_id');
                $table->unsignedBigInteger('creative_id')->nullable();
                $table->unsignedBigInteger('publisher_id');
                $table->unsignedBigInteger('advertiser_id');
                $table->string('event_type', 24);
                $table->string('visitor_key', 80);
                $table->string('country_code', 8)->default('ZZ');
                $table->string('device_type', 16)->default('desktop');
                $table->text('referrer')->nullable();
                $table->string('ip_hash', 80)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('occurred_at');

                $table->index(['placement_id', 'event_type', 'occurred_at'], 'custom_ad_events_placement_type_date_idx');
                $table->index(['deal_id', 'event_type', 'occurred_at'], 'custom_ad_events_deal_type_date_idx');
                $table->index(['creative_id', 'event_type', 'occurred_at'], 'custom_ad_events_creative_type_date_idx');
            });
        }

        if (!Schema::hasTable('custom_ad_payouts')) {
            Schema::create('custom_ad_payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('deal_id');
                $table->unsignedBigInteger('publisher_id');
                $table->unsignedBigInteger('advertiser_id');
                $table->string('type', 24)->default('daily');
                $table->decimal('amount', 12, 2);
                $table->date('payout_date')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['deal_id', 'type', 'payout_date'], 'custom_ad_payouts_unique_day_idx');
                $table->index(['publisher_id', 'type'], 'custom_ad_payouts_publisher_type_idx');
                $table->index(['advertiser_id', 'type'], 'custom_ad_payouts_advertiser_type_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_ad_payouts');
        Schema::dropIfExists('custom_ad_events');
        Schema::dropIfExists('custom_ad_creatives');
        Schema::dropIfExists('custom_ad_deals');
        Schema::dropIfExists('custom_ad_placements');
    }
};
