<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seo_settings')) {
            Schema::create('seo_settings', function (Blueprint $table) {
                $table->id();
                $table->string('default_title')->nullable();
                $table->text('default_description')->nullable();
                $table->text('default_keywords')->nullable();
                $table->string('default_robots')->default('index,follow,max-image-preview:large');
                $table->string('canonical_mode')->default('strip_tracking');
                $table->string('default_og_image')->nullable();
                $table->string('default_twitter_card')->default('summary_large_image');
                $table->boolean('ga4_enabled')->default(false);
                $table->string('ga4_measurement_id')->nullable();
                $table->string('google_site_verification')->nullable();
                $table->string('bing_site_verification')->nullable();
                $table->string('yandex_site_verification')->nullable();
                $table->boolean('allow_indexing')->default(true);
                $table->text('robots_allow_paths')->nullable();
                $table->text('robots_disallow_paths')->nullable();
                $table->text('robots_extra')->nullable();
                $table->longText('head_snippets')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('seo_rules')) {
            Schema::create('seo_rules', function (Blueprint $table) {
                $table->id();
                $table->string('scope_key');
                $table->string('content_type')->nullable();
                $table->unsignedBigInteger('content_id')->nullable();
                $table->text('title')->nullable();
                $table->text('description')->nullable();
                $table->text('keywords')->nullable();
                $table->string('robots')->nullable();
                $table->string('canonical_url')->nullable();
                $table->text('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image_url')->nullable();
                $table->string('twitter_card')->nullable();
                $table->string('schema_type')->nullable();
                $table->boolean('indexable')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['scope_key', 'content_type', 'content_id'], 'seo_rules_lookup_index');
            });
        }

        if (!Schema::hasTable('seo_daily_metrics')) {
            Schema::create('seo_daily_metrics', function (Blueprint $table) {
                $table->id();
                $table->date('metric_date');
                $table->string('scope_type')->default('route');
                $table->string('scope_key');
                $table->string('content_type')->nullable();
                $table->unsignedBigInteger('content_id')->nullable();
                $table->unsignedBigInteger('page_views')->default(0);
                $table->unsignedBigInteger('unique_visitors')->default(0);
                $table->unsignedBigInteger('bot_hits')->default(0);
                $table->unsignedBigInteger('status_404')->default(0);
                $table->timestamps();

                $table->index(['metric_date', 'scope_key'], 'seo_daily_metrics_date_scope_index');
                $table->index(['content_type', 'content_id'], 'seo_daily_metrics_content_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_daily_metrics');
        Schema::dropIfExists('seo_rules');
        Schema::dropIfExists('seo_settings');
    }
};
