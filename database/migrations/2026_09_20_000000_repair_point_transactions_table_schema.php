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
        if (!Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 12, 2);
                $table->decimal('balance_after', 12, 2)->nullable();
                $table->string('type');
                $table->string('description_key')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index(['reference_type', 'reference_id']);
                $table->index('type');
            });

            return;
        }

        Schema::table('point_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('point_transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('point_transactions', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('user_id');
            }
            if (!Schema::hasColumn('point_transactions', 'balance_after')) {
                $table->decimal('balance_after', 12, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('point_transactions', 'type')) {
                $table->string('type')->default('legacy')->after('balance_after');
            }
            if (!Schema::hasColumn('point_transactions', 'description_key')) {
                $table->string('description_key')->nullable()->after('type');
            }
            if (!Schema::hasColumn('point_transactions', 'reference_type')) {
                $table->string('reference_type')->nullable()->after('description_key');
            }
            if (!Schema::hasColumn('point_transactions', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }
            if (!Schema::hasColumn('point_transactions', 'meta')) {
                $table->json('meta')->nullable()->after('reference_id');
            }
            if (!Schema::hasColumn('point_transactions', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('meta');
            }
            if (!Schema::hasColumn('point_transactions', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive repair migration.
    }
};
