<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->unsignedInteger('duration_days')->nullable();
                $table->boolean('is_lifetime')->default(false)->index();
                $table->decimal('base_price', 12, 2)->default(0);
                $table->boolean('is_featured')->default(false)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->string('accent_color', 20)->nullable();
                $table->string('recommended_text', 100)->nullable();
                $table->json('marketing_bullets')->nullable();
                $table->json('entitlements')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('billing_currencies')) {
            Schema::create('billing_currencies', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('code', 10)->unique();
                $table->string('name', 100)->nullable();
                $table->string('symbol', 16)->nullable();
                $table->decimal('exchange_rate', 16, 6)->default(1);
                $table->unsignedTinyInteger('decimal_places')->default(2);
                $table->boolean('is_active')->default(true)->index();
                $table->boolean('is_base')->default(false)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });

            try {
                DB::table('billing_currencies')->insert([
                    [
                        'code' => 'USD',
                        'name' => 'US Dollar',
                        'symbol' => '$',
                        'exchange_rate' => 1,
                        'decimal_places' => 2,
                        'is_active' => true,
                        'is_base' => true,
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'code' => 'EUR',
                        'name' => 'Euro',
                        'symbol' => 'EUR',
                        'exchange_rate' => 0.92,
                        'decimal_places' => 2,
                        'is_active' => true,
                        'is_base' => false,
                        'sort_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'code' => 'GBP',
                        'name' => 'Pound Sterling',
                        'symbol' => 'GBP',
                        'exchange_rate' => 0.79,
                        'decimal_places' => 2,
                        'is_active' => true,
                        'is_base' => false,
                        'sort_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            } catch (\Throwable) {
                // Ignore default-currency seeding failures on partial upgrades.
            }
        }

        if (!Schema::hasTable('billing_orders')) {
            Schema::create('billing_orders', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('order_number', 50)->unique();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('subscription_plan_id')->nullable()->index();
                $table->unsignedBigInteger('member_subscription_id')->nullable()->index();
                $table->string('gateway', 40)->index();
                $table->string('status', 40)->default('pending_checkout')->index();
                $table->string('base_currency_code', 10)->default('USD');
                $table->string('currency_code', 10)->default('USD')->index();
                $table->decimal('base_amount', 12, 2)->default(0);
                $table->decimal('display_amount', 12, 2)->default(0);
                $table->decimal('exchange_rate_snapshot', 16, 6)->default(1);
                $table->string('gateway_checkout_reference', 191)->nullable()->index();
                $table->string('gateway_reference', 191)->nullable()->index();
                $table->string('receipt_path', 255)->nullable();
                $table->string('receipt_note', 500)->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamp('paid_at')->nullable()->index();
                $table->timestamp('approved_at')->nullable()->index();
                $table->timestamp('rejected_at')->nullable()->index();
                $table->timestamp('expires_at')->nullable()->index();
                $table->json('plan_snapshot')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('member_subscriptions')) {
            Schema::create('member_subscriptions', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('subscription_plan_id')->nullable()->index();
                $table->unsignedBigInteger('billing_order_id')->nullable()->index();
                $table->unsignedBigInteger('queued_from_subscription_id')->nullable()->index();
                $table->string('status', 40)->default('active')->index();
                $table->string('plan_name', 150)->nullable();
                $table->json('plan_snapshot')->nullable();
                $table->json('entitlements_snapshot')->nullable();
                $table->timestamp('starts_at')->nullable()->index();
                $table->timestamp('ends_at')->nullable()->index();
                $table->timestamp('activated_at')->nullable()->index();
                $table->timestamp('benefits_applied_at')->nullable()->index();
                $table->timestamp('completed_at')->nullable()->index();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('billing_transactions')) {
            Schema::create('billing_transactions', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('billing_order_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('gateway', 40)->nullable()->index();
                $table->string('transaction_type', 40)->default('event')->index();
                $table->string('status', 40)->default('pending')->index();
                $table->string('external_transaction_id', 191)->nullable()->index();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('currency_code', 10)->nullable()->index();
                $table->decimal('exchange_rate_snapshot', 16, 6)->default(1);
                $table->timestamp('processed_at')->nullable()->index();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('billing_transactions')) {
            Schema::drop('billing_transactions');
        }

        if (Schema::hasTable('member_subscriptions')) {
            Schema::drop('member_subscriptions');
        }

        if (Schema::hasTable('billing_orders')) {
            Schema::drop('billing_orders');
        }

        if (Schema::hasTable('billing_currencies')) {
            Schema::drop('billing_currencies');
        }

        if (Schema::hasTable('subscription_plans')) {
            Schema::drop('subscription_plans');
        }
    }
};
