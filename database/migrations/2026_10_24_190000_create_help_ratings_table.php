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
        if (!Schema::hasTable('help_ratings')) {
            Schema::create('help_ratings', function (Blueprint $table) {
                $table->id();
                $table->string('article_slug', 100)->index();
                $table->boolean('is_helpful')->default(true);
                $table->string('ip_address', 45)->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->timestamps();

                $table->index(['article_slug', 'ip_address']);
                $table->index(['article_slug', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_ratings');
    }
};
