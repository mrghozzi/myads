<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('options', 'updated_at')) {
            return;
        }

        Schema::table('options', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('options', 'updated_at')) {
            return;
        }

        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
