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
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->string('title', 150)->nullable();
                $table->text('comment')->nullable();
                $table->boolean('is_verified_buyer')->default(false);
                $table->timestamps();

                $table->unique(['product_id', 'user_id'], 'prod_reviews_user_unique');
            });
        }

        if (!Schema::hasTable('product_media')) {
            Schema::create('product_media', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('media_type', 30)->default('screenshot')->index(); // screenshot, video, demo_url
                $table->text('url');
                $table->string('caption', 255)->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('product_reviews');
    }
};
