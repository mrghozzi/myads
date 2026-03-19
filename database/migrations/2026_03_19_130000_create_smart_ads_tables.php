<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nsmart')) {
                $table->decimal('nsmart', 10, 2)->default(0);
            }
        });

        if (!Schema::hasTable('smart_ads')) {
            Schema::create('smart_ads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('landing_url');
                $table->string('headline_override')->nullable();
                $table->text('description_override')->nullable();
                $table->string('image')->nullable();
                $table->text('countries')->nullable();
                $table->text('devices')->nullable();
                $table->text('manual_keywords')->nullable();
                $table->text('extracted_keywords')->nullable();
                $table->string('source_title')->nullable();
                $table->text('source_description')->nullable();
                $table->string('source_image')->nullable();
                $table->integer('impressions')->default(0);
                $table->integer('clicks')->default(0);
                $table->integer('statu')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('smart_ad_impressions')) {
            Schema::create('smart_ad_impressions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('smart_ad_id');
                $table->unsignedBigInteger('publisher_id');
                $table->string('visitor_key', 80);
                $table->string('country_code', 8)->default('ZZ');
                $table->string('device_type', 16)->default('desktop');
                $table->string('placement', 24)->default('native');
                $table->unsignedBigInteger('served_at');

                $table->index(['publisher_id', 'visitor_key', 'served_at'], 'smart_impressions_pub_visitor_served_idx');
                $table->index(['smart_ad_id', 'served_at'], 'smart_impressions_ad_served_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_ad_impressions');
        Schema::dropIfExists('smart_ads');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nsmart')) {
                $table->dropColumn('nsmart');
            }
        });
    }
};
