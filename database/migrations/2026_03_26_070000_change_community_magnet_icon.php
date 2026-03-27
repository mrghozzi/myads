<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('badges')->where('slug', 'community_magnet')->update(['icon' => 'fa-magnet']);
    }

    public function down(): void
    {
        DB::table('badges')->where('slug', 'community_magnet')->update(['icon' => 'fa-users']);
    }
};
