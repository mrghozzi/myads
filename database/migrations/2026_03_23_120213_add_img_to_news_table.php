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
        if (!Schema::hasTable('news')) {
            return;
        }

        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'img')) {
                $table->string('img')->nullable()->after('text');
            }

            if (!Schema::hasColumn('news', 'statu')) {
                $table->integer('statu')->default(1)->after('img');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('news')) {
            return;
        }

        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'statu')) {
                $table->dropColumn('statu');
            }

            if (Schema::hasColumn('news', 'img')) {
                $table->dropColumn('img');
            }
        });
    }
};
