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
        Schema::table('banner', function (Blueprint $table) {
            $table->text('countries')->nullable()->after('px');
            $table->text('devices')->nullable()->after('countries');
        });

        Schema::table('link', function (Blueprint $table) {
            $table->text('countries')->nullable()->after('txt');
            $table->text('devices')->nullable()->after('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->dropColumn(['countries', 'devices']);
        });

        Schema::table('link', function (Blueprint $table) {
            $table->dropColumn(['countries', 'devices']);
        });
    }
};
