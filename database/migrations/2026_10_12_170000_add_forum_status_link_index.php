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
        if (Schema::hasTable('status')) {
            if (!Schema::hasIndex('status', 'status_tp_id_stype_idx')) {
                try {
                    Schema::table('status', function (Blueprint $table) {
                        $table->index(['tp_id', 's_type'], 'status_tp_id_stype_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not create status_tp_id_stype_idx: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('status')) {
            if (Schema::hasIndex('status', 'status_tp_id_stype_idx')) {
                try {
                    Schema::table('status', function (Blueprint $table) {
                        $table->dropIndex('status_tp_id_stype_idx');
                    });
                } catch (\Throwable $e) {
                    \Log::warning('Could not drop status_tp_id_stype_idx: ' . $e->getMessage());
                }
            }
        }
    }
};
