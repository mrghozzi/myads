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
        Schema::table('f_cat', function (Blueprint $table) {
            $table->text('txt')->nullable()->change();
        });

        Schema::table('cat_dir', function (Blueprint $table) {
            $table->text('txt')->nullable()->change();
            $table->text('metakeywords')->nullable()->change();
        });

        Schema::table('directory', function (Blueprint $table) {
            $table->text('txt')->nullable()->change();
            $table->text('metakeywords')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op as this is a repair migration to fix strict mode issues
    }
};
