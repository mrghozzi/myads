<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forum_moderators')) {
            return;
        }

        Schema::create('forum_moderators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('is_global')->default(false);
            $table->text('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('is_global');
            $table->index('is_active');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_moderators');
    }
};