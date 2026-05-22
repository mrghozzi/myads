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
        Schema::create('store_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('sale_price');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            $table->index('product_id');
        });

        Schema::create('store_discount_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // null means admin/global
            $table->string('name', 255);
            $table->string('code', 100)->unique();
            $table->string('discount_type', 50); // 'percent' or 'fixed'
            $table->integer('discount_value');
            $table->string('applies_to', 50); // 'all', 'product', 'category', 'seller'
            $table->string('target_value', 255)->nullable(); // product ID, category name, or seller ID
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('uses')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('store_discount_redemptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('discount_code_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('points_saved');
            $table->timestamp('created_at')->nullable();

            $table->index('user_id');
            $table->index('discount_code_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_discount_redemptions');
        Schema::dropIfExists('store_discount_codes');
        Schema::dropIfExists('store_sales');
    }
};
