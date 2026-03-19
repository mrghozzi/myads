<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('banner')) {
            $this->ensureBannerPxColumnSupportsCanonicalSizes();

            DB::table('banner')->where('px', '468')->update(['px' => '468x60']);
            DB::table('banner')->where('px', '728')->update(['px' => '728x90']);
            DB::table('banner')->where('px', '300')->update(['px' => '300x250']);
            DB::table('banner')->where('px', '160')->update(['px' => '160x600']);
        }

        if (!Schema::hasTable('banner_impressions')) {
            Schema::create('banner_impressions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('banner_id');
                $table->unsignedBigInteger('publisher_id');
                $table->string('visitor_key', 80);
                $table->bigInteger('served_at');

                $table->index(['publisher_id', 'visitor_key', 'served_at'], 'banner_impressions_lookup_idx');
                $table->index(['banner_id', 'publisher_id', 'visitor_key'], 'banner_impressions_banner_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_impressions');
    }

    private function ensureBannerPxColumnSupportsCanonicalSizes(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        $column = DB::selectOne("SHOW COLUMNS FROM `banner` LIKE 'px'");

        if (!$column || !isset($column->Type)) {
            return;
        }

        $type = strtolower((string) $column->Type);

        if (str_contains($type, 'char') || str_contains($type, 'text')) {
            return;
        }

        DB::statement('ALTER TABLE `banner` MODIFY `px` VARCHAR(20) NOT NULL');
    }
};
