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
        if (Schema::hasTable('directory')) {
            Schema::table('directory', function (Blueprint $table) {
                if (!Schema::hasColumn('directory', 'date')) {
                    $table->bigInteger('date')->default(0)->after('statu');
                }
                
                // Ensure other expected columns from the core migration also exist
                // based on the 2026_02_27_000000_create_core_application_tables.php
                if (!Schema::hasColumn('directory', 'uid')) {
                    $table->unsignedBigInteger('uid')->after('id');
                }
                if (!Schema::hasColumn('directory', 'name')) {
                    $table->string('name')->after('uid');
                }
                if (!Schema::hasColumn('directory', 'url')) {
                    $table->string('url')->after('name');
                }
                if (!Schema::hasColumn('directory', 'txt')) {
                    $table->text('txt')->nullable()->after('url');
                }
                if (!Schema::hasColumn('directory', 'metakeywords')) {
                    $table->string('metakeywords')->nullable()->after('txt');
                }
                if (!Schema::hasColumn('directory', 'cat')) {
                    $table->unsignedBigInteger('cat')->default(0)->after('metakeywords');
                }
                if (!Schema::hasColumn('directory', 'vu')) {
                    $table->integer('vu')->default(0)->after('cat');
                }
                if (!Schema::hasColumn('directory', 'statu')) {
                    $table->tinyInteger('statu')->default(1)->after('vu');
                }
            });
        } elseif (!Schema::hasTable('directories')) {
            // If neither 'directory' nor 'directories' exists (unlikely given the error),
            // we should probably let the core migration handle it, but for safety:
            Schema::create('directory', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('name');
                $table->string('url');
                $table->text('txt')->nullable();
                $table->string('metakeywords')->nullable();
                $table->unsignedBigInteger('cat')->default(0);
                $table->integer('vu')->default(0);
                $table->tinyInteger('statu')->default(1);
                $table->bigInteger('date')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Repair migration; intentionally minimal down() to avoid accidental data loss.
    }
};
