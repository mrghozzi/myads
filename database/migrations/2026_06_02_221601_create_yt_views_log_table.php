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
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\Schema::dropIfExists('yt_views_log');
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        Schema::create('yt_views_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreignId('video_id')->constrained('yt_videos')->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->timestamp('watched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yt_views_log');
    }
};
