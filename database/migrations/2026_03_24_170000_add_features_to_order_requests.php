<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('best_offer_id')->nullable()->after('category');
            $table->unsignedBigInteger('last_activity')->nullable()->after('date');
            $table->decimal('avg_rating', 3, 2)->default(0)->after('last_activity');
        });

        // We also want to store ratings in the options table for each offer
        // If the options table is already complex, we might want to just use o_mode as an integer rating.
        // It already exists, but we can add a comment or just use it.
    }

    public function down(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->dropColumn(['best_offer_id', 'last_activity', 'avg_rating']);
        });
    }
};
