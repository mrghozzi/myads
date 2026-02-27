<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('banner')) {
            Schema::create('banner', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('name');
                $table->string('url');
                $table->string('img');
                $table->string('px');
                $table->integer('vu')->default(0);
                $table->integer('clik')->default(0);
                $table->integer('statu')->default(1);
            });
        }

        if (!Schema::hasTable('link')) {
            Schema::create('link', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('name');
                $table->string('url');
                $table->text('txt')->nullable();
                $table->integer('clik')->default(0);
                $table->integer('statu')->default(1);
            });
        }

        if (!Schema::hasTable('visits')) {
            Schema::create('visits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('name');
                $table->string('url');
                $table->string('tims');
                $table->integer('vu')->default(0);
                $table->integer('statu')->default(1);
            });
        }

        if (!Schema::hasTable('state')) {
            Schema::create('state', function (Blueprint $table) {
                $table->id();
                $table->integer('sid');
                $table->integer('pid');
                $table->string('t_name');
                $table->string('r_link');
                $table->bigInteger('r_date');
                $table->text('visitor_Agent');
                $table->string('v_ip');
            });
        }

        if (!Schema::hasTable('options')) {
            Schema::create('options', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('o_valuer')->nullable();
                $table->string('o_type');
                $table->integer('o_parent')->default(0);
                $table->integer('o_order')->default(0);
                $table->string('o_mode')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banner');
        Schema::dropIfExists('link');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('state');
        Schema::dropIfExists('options');
    }
};
