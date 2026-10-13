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
        if (Schema::hasTable('f_cat') && !Schema::hasColumn('f_cat', 'parent_id')) {
            Schema::table('f_cat', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->default(null)->after('id');
                $table->index('parent_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('f_cat') && Schema::hasColumn('f_cat', 'parent_id')) {
            Schema::table('f_cat', function (Blueprint $table) {
                $table->dropIndex(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }
    }
};
