<?php

use App\Support\OrderMarketplaceBackfill;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_offers')) {
            Schema::create('order_offers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_request_id');
                $table->unsignedBigInteger('user_id');
                $table->string('pricing_model', 20)->default('fixed');
                $table->decimal('quoted_amount', 12, 2)->nullable();
                $table->string('currency_code', 10)->default('USD');
                $table->unsignedInteger('delivery_days')->nullable();
                $table->text('message');
                $table->string('status', 20)->default('active');
                $table->unsignedTinyInteger('client_rating')->nullable();
                $table->text('client_review')->nullable();
                $table->unsignedBigInteger('legacy_option_id')->nullable();
                $table->string('legacy_option_type', 30)->nullable();
                $table->timestamp('awarded_at')->nullable();
                $table->timestamp('withdrawn_at')->nullable();
                $table->timestamp('rated_at')->nullable();
                $table->timestamps();

                $table->index(['order_request_id', 'status']);
                $table->index(['user_id', 'status']);
                $table->index(['legacy_option_type', 'legacy_option_id']);
            });
        }

        if (!Schema::hasTable('order_contracts')) {
            Schema::create('order_contracts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_request_id')->unique();
                $table->unsignedBigInteger('order_offer_id');
                $table->unsignedBigInteger('client_user_id');
                $table->unsignedBigInteger('provider_user_id');
                $table->string('status', 20)->default('awarded');
                $table->string('pricing_model', 20)->default('fixed');
                $table->decimal('quoted_amount', 12, 2)->nullable();
                $table->string('currency_code', 10)->default('USD');
                $table->unsignedInteger('delivery_days')->nullable();
                $table->longText('snapshot_payload')->nullable();
                $table->longText('delivery_note')->nullable();
                $table->longText('completion_note')->nullable();
                $table->timestamp('awarded_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();

                $table->index(['provider_user_id', 'status']);
                $table->index(['client_user_id', 'status']);
            });
        }

        if (Schema::hasTable('order_requests')) {
            Schema::table('order_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('order_requests', 'pricing_model')) {
                    $table->string('pricing_model', 20)->default('fixed')->after('category');
                }
                if (!Schema::hasColumn('order_requests', 'budget_min')) {
                    $table->decimal('budget_min', 12, 2)->nullable()->after('budget');
                }
                if (!Schema::hasColumn('order_requests', 'budget_max')) {
                    $table->decimal('budget_max', 12, 2)->nullable()->after('budget_min');
                }
                if (!Schema::hasColumn('order_requests', 'budget_currency')) {
                    $table->string('budget_currency', 10)->default('USD')->after('budget_max');
                }
                if (!Schema::hasColumn('order_requests', 'delivery_window_days')) {
                    $table->unsignedInteger('delivery_window_days')->nullable()->after('budget_currency');
                }
                if (!Schema::hasColumn('order_requests', 'workflow_status')) {
                    $table->string('workflow_status', 20)->default('open')->after('avg_rating');
                }
            });
        }

        OrderMarketplaceBackfill::run();
    }

    public function down(): void
    {
        if (Schema::hasTable('order_requests')) {
            Schema::table('order_requests', function (Blueprint $table) {
                foreach (['pricing_model', 'budget_min', 'budget_max', 'budget_currency', 'delivery_window_days', 'workflow_status'] as $column) {
                    if (Schema::hasColumn('order_requests', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('order_contracts');
        Schema::dropIfExists('order_offers');
    }
};
