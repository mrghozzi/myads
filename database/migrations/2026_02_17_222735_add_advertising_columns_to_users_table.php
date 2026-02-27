<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pts')) {
                $table->decimal('pts', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'nvu')) {
                $table->decimal('nvu', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'nlink')) {
                $table->decimal('nlink', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'vu')) {
                $table->decimal('vu', 10, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pts')) $table->dropColumn('pts');
            if (Schema::hasColumn('users', 'nvu')) $table->dropColumn('nvu');
            if (Schema::hasColumn('users', 'nlink')) $table->dropColumn('nlink');
            if (Schema::hasColumn('users', 'vu')) $table->dropColumn('vu');
        });
    }
};
