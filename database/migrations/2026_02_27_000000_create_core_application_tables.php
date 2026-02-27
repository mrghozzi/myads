<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create all core application tables that are missing from the base migrations.
 * Tables covered: setting, menu, f_cat, forum, f_coment, ads, like, notif,
 *                 messages, report, short, news, emojis, status, cat_dir, referral
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Site Settings ──────────────────────────────────────────
        if (!Schema::hasTable('setting')) {
            Schema::create('setting', function (Blueprint $table) {
                $table->id();
                $table->string('titer')->default('MyAds');
                $table->text('description')->nullable();
                $table->string('url')->nullable();
                $table->string('styles')->default('default');
                $table->string('lang')->default('en');
                $table->string('timezone')->default('UTC');
                $table->tinyInteger('close')->default(1);
                $table->text('close_text')->nullable();
                $table->string('a_mail')->nullable();
                $table->string('a_not')->nullable();
                $table->tinyInteger('e_links')->default(1);
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('linkedin')->nullable();
            });
        }

        // ── Navigation Menus ───────────────────────────────────────
        if (!Schema::hasTable('menu')) {
            Schema::create('menu', function (Blueprint $table) {
                $table->increments('id_m');
                $table->string('name');
                $table->string('dir')->nullable();
                $table->string('type')->nullable();
            });
        }

        // ── Forum Categories ───────────────────────────────────────
        if (!Schema::hasTable('f_cat')) {
            Schema::create('f_cat', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icons')->nullable();
                $table->text('txt')->nullable();
                $table->integer('ordercat')->default(0);
            });
        }

        // ── Forum Topics ───────────────────────────────────────────
        if (!Schema::hasTable('forum')) {
            Schema::create('forum', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('name');
                $table->text('txt')->nullable();
                $table->unsignedBigInteger('cat')->default(0);
                $table->tinyInteger('statu')->default(1);
                $table->bigInteger('date')->default(0);
                $table->integer('reply')->default(0);
                $table->integer('vu')->default(0);
            });
        }

        // ── Forum Comments ─────────────────────────────────────────
        if (!Schema::hasTable('f_coment')) {
            Schema::create('f_coment', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->unsignedBigInteger('tid');
                $table->text('txt')->nullable();
                $table->bigInteger('date')->default(0);
            });
        }

        // ── Site Ads (Admin Slots) ─────────────────────────────────
        if (!Schema::hasTable('ads')) {
            Schema::create('ads', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('code_ads')->nullable();
            });
        }

        // ── Likes / Follows / Reactions ────────────────────────────
        if (!Schema::hasTable('like')) {
            Schema::create('like', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->unsignedBigInteger('sid');
                $table->integer('type')->default(0); // 1=Follow, 2=Forum, 22=Dir, 3=Store
                $table->bigInteger('time_t')->default(0);
            });
        }

        // ── Notifications ──────────────────────────────────────────
        if (!Schema::hasTable('notif')) {
            Schema::create('notif', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('name')->nullable();
                $table->string('nurl')->nullable();
                $table->string('logo')->nullable();
                $table->bigInteger('time')->default(0);
                $table->tinyInteger('state')->default(0);
            });
        }

        // ── Private Messages ───────────────────────────────────────
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->increments('id_msg');
                $table->string('name')->nullable();
                $table->unsignedBigInteger('us_env'); // Sender
                $table->unsignedBigInteger('us_rec'); // Receiver
                $table->text('msg')->nullable();
                $table->bigInteger('time')->default(0);
                $table->tinyInteger('state')->default(0); // 0=new,1=read
            });
        }

        // ── Reports ────────────────────────────────────────────────
        if (!Schema::hasTable('report')) {
            Schema::create('report', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->integer('s_type')->default(0);
                $table->unsignedBigInteger('tp_id');
                $table->text('txt')->nullable();
                $table->tinyInteger('statu')->default(0);
            });
        }

        // ── Short URLs ─────────────────────────────────────────────
        if (!Schema::hasTable('short')) {
            Schema::create('short', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid')->default(0);
                $table->text('url');
                $table->string('sho')->unique();
                $table->integer('clik')->default(0);
                $table->integer('sh_type')->default(0);
                $table->unsignedBigInteger('tp_id')->default(0);
            });
        }

        // ── News ───────────────────────────────────────────────────
        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('text')->nullable();
                $table->bigInteger('date')->default(0);
                $table->string('img')->nullable();
            });
        }

        // ── Emojis ─────────────────────────────────────────────────
        if (!Schema::hasTable('emojis')) {
            Schema::create('emojis', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('img');
            });
        }

        // ── Status Feed ────────────────────────────────────────────
        if (!Schema::hasTable('status')) {
            Schema::create('status', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->unsignedBigInteger('tp_id')->default(0);
                $table->integer('s_type')->default(100); // 100=post, 1=dir, 2=forum topic, etc.
                $table->bigInteger('date')->default(0);
                $table->text('txt')->nullable();
                $table->tinyInteger('statu')->default(1);
            });
        }

        // ── Directory Categories ───────────────────────────────────
        if (!Schema::hasTable('cat_dir')) {
            Schema::create('cat_dir', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('sub')->default(0); // Parent category ID
                $table->integer('ordercat')->default(0);
                $table->tinyInteger('statu')->default(1);
                $table->text('txt')->nullable();
                $table->string('metakeywords')->nullable();
            });
        }

        // ── Referrals ──────────────────────────────────────────────
        if (!Schema::hasTable('referral')) {
            Schema::create('referral', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');  // Referrer
                $table->unsignedBigInteger('ruid'); // Referred user
                $table->bigInteger('date')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('referral');
        Schema::dropIfExists('cat_dir');
        Schema::dropIfExists('status');
        Schema::dropIfExists('emojis');
        Schema::dropIfExists('news');
        Schema::dropIfExists('short');
        Schema::dropIfExists('report');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('notif');
        Schema::dropIfExists('like');
        Schema::dropIfExists('ads');
        Schema::dropIfExists('f_coment');
        Schema::dropIfExists('forum');
        Schema::dropIfExists('f_cat');
        Schema::dropIfExists('menu');
        Schema::dropIfExists('setting');
    }
};
