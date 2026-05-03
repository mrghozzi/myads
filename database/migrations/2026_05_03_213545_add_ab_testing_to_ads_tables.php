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
            $table->string('img_b')->nullable()->after('img');
            $table->integer('vu_a')->default(0)->after('clik');
            $table->integer('clik_a')->default(0)->after('vu_a');
            $table->integer('vu_b')->default(0)->after('clik_a');
            $table->integer('clik_b')->default(0)->after('vu_b');
        });

        Schema::table('link', function (Blueprint $table) {
            $table->string('name_b')->nullable()->after('name');
            $table->mediumText('txt_b')->nullable()->after('txt');
            $table->integer('vu_a')->default(0)->after('clik');
            $table->integer('clik_a')->default(0)->after('vu_a');
            $table->integer('vu_b')->default(0)->after('clik_a');
            $table->integer('clik_b')->default(0)->after('vu_b');
            
            // Link table didn't have total 'vu', but we use vu_a and vu_b for A/B tracking.
            // We could add a total 'vu' column if needed for legacy stats, but vu_a + vu_b is enough.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->dropColumn(['img_b', 'vu_a', 'clik_a', 'vu_b', 'clik_b']);
        });

        Schema::table('link', function (Blueprint $table) {
            $table->dropColumn(['name_b', 'txt_b', 'vu_a', 'clik_a', 'vu_b', 'clik_b']);
        });
    }
};
