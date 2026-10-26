<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-solid fa-circle-question');
            $table->string('color', 30)->default('#615dfa');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('topics_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->timestamps();
        });

        Schema::create('support_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->string('slug')->index();
            $table->longText('content');
            $table->string('status', 20)->default('open'); // open, answered, resolved, closed
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->unsignedBigInteger('best_reply_id')->nullable();
            $table->timestamp('last_reply_at')->nullable();
            $table->unsignedBigInteger('last_reply_user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('support_categories')->cascadeOnDelete();
            $table->index(['category_id', 'is_pinned', 'created_at']);
        });

        Schema::create('support_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('topic_id');
            $table->unsignedBigInteger('user_id');
            $table->longText('content');
            $table->boolean('is_staff')->default(false);
            $table->boolean('is_solution')->default(false);
            $table->timestamps();

            $table->foreign('topic_id')->references('id')->on('support_topics')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['topic_id', 'created_at']);
        });

        // Seed initial support categories
        $now = now();
        DB::table('support_categories')->insert([
            [
                'name' => 'أسئلة عامة وإرشادات الاستخدام',
                'slug' => 'general-inquiries',
                'description' => 'استفسارات المبتدئين، إرشادات المنصة العامة، والنصائح الأساسية للأعضاء.',
                'icon' => 'fa-solid fa-compass',
                'color' => '#615dfa',
                'sort_order' => 1,
                'is_active' => true,
                'topics_count' => 0,
                'replies_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'مشاكل تقنية وحلول',
                'slug' => 'technical-issues',
                'description' => 'حل المشكلات البرمجية، الأعطال، استفسارات التصفح وتبادل الزيارات.',
                'icon' => 'fa-solid fa-wrench',
                'color' => '#e67e22',
                'sort_order' => 2,
                'is_active' => true,
                'topics_count' => 0,
                'replies_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'الحسابات والفواتير والمدفوعات',
                'slug' => 'accounts-and-billing',
                'description' => 'استفسارات المحفظة، عمليات السحب والإيداع، والترقيات والاشتراكات.',
                'icon' => 'fa-solid fa-credit-card',
                'color' => '#10b981',
                'sort_order' => 3,
                'is_active' => true,
                'topics_count' => 0,
                'replies_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'اقتراحات وتطوير المنصة',
                'slug' => 'suggestions-and-feedback',
                'description' => 'شارك أفكارك ومقترحاتك لتطوير الخدمات وإضافة ميزات جديدة للموقع.',
                'icon' => 'fa-solid fa-lightbulb',
                'color' => '#f59e0b',
                'sort_order' => 4,
                'is_active' => true,
                'topics_count' => 0,
                'replies_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'إعلانات وتحديثات الدعم الفني',
                'slug' => 'support-announcements',
                'description' => 'أحدث تنبيهات الإدارة، تحديثات الأمان، والإعلانات الهامة للأعضاء.',
                'icon' => 'fa-solid fa-bullhorn',
                'color' => '#23d2e2',
                'sort_order' => 5,
                'is_active' => true,
                'topics_count' => 0,
                'replies_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_replies');
        Schema::dropIfExists('support_topics');
        Schema::dropIfExists('support_categories');
    }
};
