<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kb_categories')) {
            Schema::create('kb_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('options', 'kb_category_id')) {
            Schema::table('options', function (Blueprint $table) {
                $table->unsignedBigInteger('kb_category_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('options', 'kb_category_id')) {
            Schema::table('options', function (Blueprint $table) {
                $table->dropColumn('kb_category_id');
            });
        }

        Schema::dropIfExists('kb_categories');
    }
};
