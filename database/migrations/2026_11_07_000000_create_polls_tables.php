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
        if (!Schema::hasTable('polls')) {
            Schema::create('polls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('status_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('question', 500);
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('poll_options')) {
            Schema::create('poll_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('poll_id')->index();
                $table->string('option_text', 255);
                $table->unsignedInteger('votes_count')->default(0);
                $table->unsignedTinyInteger('order')->default(0);
                $table->timestamps();

                $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('poll_votes')) {
            Schema::create('poll_votes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('poll_id')->index();
                $table->unsignedBigInteger('poll_option_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->unique(['poll_id', 'user_id']);
                $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
                $table->foreign('poll_option_id')->references('id')->on('poll_options')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
    }
};

