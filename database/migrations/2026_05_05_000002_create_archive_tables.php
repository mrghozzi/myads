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
        if (!Schema::hasTable('banner_impressions_archive')) {
            Schema::create('banner_impressions_archive', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('banner_id')->index();
                $table->unsignedBigInteger('publisher_id')->index();
                $table->string('visitor_key', 80);
                $table->bigInteger('served_at');
            });
        }

        if (!Schema::hasTable('smart_ad_impressions_archive')) {
            Schema::create('smart_ad_impressions_archive', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('smart_ad_id')->index();
                $table->unsignedBigInteger('publisher_id')->index();
                $table->string('visitor_key', 80);
                $table->string('country_code', 8)->default('ZZ');
                $table->string('device_type', 16)->default('desktop');
                $table->string('placement', 24)->default('native');
                $table->unsignedBigInteger('served_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner_impressions_archive');
        Schema::dropIfExists('smart_ad_impressions_archive');
    }
};
