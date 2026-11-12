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
        if (!Schema::hasTable('pts_ledger_entries')) {
            Schema::create('pts_ledger_entries', function (Blueprint $table) {
                $table->id();
                $table->uuid('transaction_uuid')->index();
                $table->string('account_type', 32)->index(); // 'user', 'system_rewards', 'system_ads', 'system_marketplace', 'system_vouchers', 'system_fees', 'system_adjustment'
                $table->string('account_id', 64)->index(); // 'user:123' or 'system:rewards'
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->decimal('amount', 14, 2); // negative for debit, positive for credit
                $table->decimal('balance_after', 14, 2)->nullable();
                $table->string('entry_type', 64)->index(); // 'transfer', 'ad_reward', 'ad_spend', 'quest_reward', etc.
                $table->string('reference_type', 64)->nullable();
                $table->string('reference_id', 64)->nullable();
                $table->string('description', 255)->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->useCurrent()->index();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->index(['account_id', 'created_at']);
                $table->index(['user_id', 'created_at']);
                $table->index(['entry_type', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pts_ledger_entries');
    }
};

