<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forum_moderator_categories')) {
            return;
        }

        Schema::create('forum_moderator_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('moderator_id');
            $table->unsignedBigInteger('category_id');

            $table->unique(['moderator_id', 'category_id'], 'forum_moderator_category_unique');
            $table->index('moderator_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_moderator_categories');
    }
};