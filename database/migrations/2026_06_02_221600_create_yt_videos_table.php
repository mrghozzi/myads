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
        if (Schema::hasTable('yt_videos')) {
            return;
        }

        Schema::create('yt_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('youtube_id');
            $table->string('title')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->unsignedInteger('duration_required');
            $table->decimal('reward_points', 15, 4);
            $table->decimal('total_budget', 15, 4);
            $table->decimal('remaining_budget', 15, 4);
            $table->enum('status', ['active', 'paused', 'completed', 'pending'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yt_videos');
    }
};
