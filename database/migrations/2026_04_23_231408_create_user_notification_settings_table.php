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
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique();
            $table->boolean('email_new_follower')->default(true);
            $table->boolean('email_new_comment')->default(true);
            $table->boolean('email_new_message')->default(true);
            $table->boolean('email_mention')->default(true);
            $table->boolean('email_repost')->default(true);
            $table->boolean('email_reaction')->default(true);
            $table->boolean('email_forum_reply')->default(true);
            $table->boolean('email_marketplace_update')->default(true);
            $table->timestamps();

            // Skip foreign key if engine is MyISAM or if it fails
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_settings');
    }
};
