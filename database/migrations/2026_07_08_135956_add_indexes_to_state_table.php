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
        Schema::table('state', function (Blueprint $table) {
            $table->index(['t_name', 'sid', 'v_ip', 'r_date', 'pid'], 'state_link_repeat_idx');
            $table->index(['pid', 't_name', 'r_date'], 'state_stats_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('state', function (Blueprint $table) {
            $table->dropIndex('state_link_repeat_idx');
            $table->dropIndex('state_stats_idx');
        });
    }
};
