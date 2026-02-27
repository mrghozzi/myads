<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('options')) {
            return;
        }

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->longText('o_valuer')->nullable();
            $table->string('o_type')->nullable();
            $table->integer('o_parent')->default(0);
            $table->integer('o_order')->default(0);
            $table->string('o_mode')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
