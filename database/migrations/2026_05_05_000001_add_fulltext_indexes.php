<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // forum (name, txt)
        try { DB::statement('ALTER TABLE forum ADD FULLTEXT forum_fulltext (name, txt)'); } catch (\Exception $e) {}

        // f_coment (txt)
        try { DB::statement('ALTER TABLE f_coment ADD FULLTEXT f_coment_fulltext (txt)'); } catch (\Exception $e) {}

        // directory (name, txt)
        try { DB::statement('ALTER TABLE directory ADD FULLTEXT directory_fulltext (name, txt)'); } catch (\Exception $e) {}

        // news (name, text)
        try { DB::statement('ALTER TABLE news ADD FULLTEXT news_fulltext (name, text)'); } catch (\Exception $e) {}

        // options (name, o_valuer)
        try { DB::statement('ALTER TABLE options ADD FULLTEXT options_fulltext (name, o_valuer)'); } catch (\Exception $e) {}

        // groups (name, description)
        try { DB::statement('ALTER TABLE groups ADD FULLTEXT groups_fulltext (name, description)'); } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE forum DROP INDEX forum_fulltext');
        DB::statement('ALTER TABLE f_coment DROP INDEX f_coment_fulltext');
        DB::statement('ALTER TABLE directory DROP INDEX directory_fulltext');
        DB::statement('ALTER TABLE news DROP INDEX news_fulltext');
        DB::statement('ALTER TABLE options DROP INDEX options_fulltext');
        try { DB::statement('ALTER TABLE groups DROP INDEX groups_fulltext'); } catch (\Exception $e) {}
    }
};
