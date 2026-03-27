<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('status_promotions')) {
            return;
        }

        Schema::create('status_promotions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('user_id');
            $table->string('objective', 20);
            $table->unsignedInteger('target_quantity');
            $table->unsignedInteger('charged_pts');
            $table->decimal('smart_factor', 5, 2)->default(1.00);
            $table->unsignedInteger('delivery_cap_impressions')->default(0);
            $table->unsignedInteger('delivered_impressions')->default(0);
            $table->unsignedInteger('baseline_comments')->default(0);
            $table->unsignedInteger('baseline_reactions')->default(0);
            $table->string('status', 20)->default('active');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('last_served_at')->nullable();
            $table->json('meta')->nullable();

            $table->index('status_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('ends_at');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('status_promotions')) {
            return;
        }

        Schema::drop('status_promotions');
    }
};
