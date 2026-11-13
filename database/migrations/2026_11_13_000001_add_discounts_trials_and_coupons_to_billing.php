<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscription_plans')) {
            Schema::table('subscription_plans', function (Blueprint $table): void {
                if (!Schema::hasColumn('subscription_plans', 'sale_price')) {
                    $table->decimal('sale_price', 12, 2)->nullable()->after('base_price');
                }
                if (!Schema::hasColumn('subscription_plans', 'sale_ends_at')) {
                    $table->timestamp('sale_ends_at')->nullable()->after('sale_price');
                }
                if (!Schema::hasColumn('subscription_plans', 'trial_days')) {
                    $table->unsignedInteger('trial_days')->nullable()->after('sale_ends_at');
                }
                if (!Schema::hasColumn('subscription_plans', 'layout_style')) {
                    $table->string('layout_style', 50)->default('default')->after('accent_color');
                }
            });
        }

        if (!Schema::hasTable('billing_coupons')) {
            Schema::create('billing_coupons', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('code', 50)->unique();
                $table->string('description', 255)->nullable();
                $table->string('discount_type', 20)->default('percent'); // 'percent' or 'fixed'
                $table->decimal('discount_value', 12, 2)->default(0);
                $table->unsignedBigInteger('subscription_plan_id')->nullable()->index();
                $table->unsignedInteger('max_uses')->nullable();
                $table->unsignedInteger('used_count')->default(0);
                $table->timestamp('expires_at')->nullable()->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();

                $table->foreign('subscription_plan_id')
                    ->references('id')
                    ->on('subscription_plans')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('billing_orders')) {
            Schema::table('billing_orders', function (Blueprint $table): void {
                if (!Schema::hasColumn('billing_orders', 'billing_coupon_id')) {
                    $table->unsignedBigInteger('billing_coupon_id')->nullable()->after('subscription_plan_id')->index();
                }
                if (!Schema::hasColumn('billing_orders', 'coupon_code')) {
                    $table->string('coupon_code', 50)->nullable()->after('billing_coupon_id');
                }
                if (!Schema::hasColumn('billing_orders', 'discount_amount')) {
                    $table->decimal('discount_amount', 12, 2)->default(0)->after('base_amount');
                }
                if (!Schema::hasColumn('billing_orders', 'is_trial')) {
                    $table->boolean('is_trial')->default(false)->after('status')->index();
                }
            });
        }

        if (Schema::hasTable('member_subscriptions')) {
            Schema::table('member_subscriptions', function (Blueprint $table): void {
                if (!Schema::hasColumn('member_subscriptions', 'is_trial')) {
                    $table->boolean('is_trial')->default(false)->after('status')->index();
                }
                if (!Schema::hasColumn('member_subscriptions', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable()->after('ends_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('billing_coupons')) {
            Schema::dropIfExists('billing_coupons');
        }

        if (Schema::hasTable('subscription_plans')) {
            Schema::table('subscription_plans', function (Blueprint $table): void {
                $columns = ['sale_price', 'sale_ends_at', 'trial_days', 'layout_style'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('subscription_plans', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('billing_orders')) {
            Schema::table('billing_orders', function (Blueprint $table): void {
                $columns = ['billing_coupon_id', 'coupon_code', 'discount_amount', 'is_trial'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('billing_orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('member_subscriptions')) {
            Schema::table('member_subscriptions', function (Blueprint $table): void {
                $columns = ['is_trial', 'trial_ends_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('member_subscriptions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
