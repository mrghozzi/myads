<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('smart_ads')) {
            return;
        }

        $this->ensureUrlColumnLength('landing_url', false);
        $this->ensureUrlColumnLength('image', true);
        $this->ensureUrlColumnLength('source_image', true);
    }

    public function down(): void
    {
        // Keep the expanded URL columns in place on rollback.
    }

    private function ensureUrlColumnLength(string $column, bool $nullable): void
    {
        if (!Schema::hasColumn('smart_ads', $column)) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $definition = DB::selectOne("SHOW COLUMNS FROM `smart_ads` LIKE '{$column}'");

        if (!$definition || !isset($definition->Type)) {
            return;
        }

        $type = strtolower((string) $definition->Type);

        if (str_contains($type, 'text')) {
            return;
        }

        if (preg_match('/varchar\((\d+)\)/', $type, $matches) === 1 && (int) $matches[1] >= 2048) {
            return;
        }

        $nullClause = $nullable ? 'NULL' : 'NOT NULL';

        DB::statement("ALTER TABLE `smart_ads` MODIFY `{$column}` VARCHAR(2048) {$nullClause}");
    }
};
