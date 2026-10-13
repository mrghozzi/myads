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
        // 1. Enhance f_cat (Forum Categories / Tags) with slug, color and description support
        if (Schema::hasTable('f_cat')) {
            Schema::table('f_cat', function (Blueprint $table) {
                if (!Schema::hasColumn('f_cat', 'slug')) {
                    $table->string('slug', 190)->nullable()->index()->after('name');
                }
                if (!Schema::hasColumn('f_cat', 'color')) {
                    $table->string('color', 32)->nullable()->default('#615dfa')->after('icons');
                }
            });
        }

        // 2. Enhance forum (Forum Topics / Discussions) with modern streaming metadata
        if (Schema::hasTable('forum')) {
            Schema::table('forum', function (Blueprint $table) {
                if (!Schema::hasColumn('forum', 'slug')) {
                    $table->string('slug', 190)->nullable()->index()->after('name');
                }
                if (!Schema::hasColumn('forum', 'last_posted_at')) {
                    $table->unsignedInteger('last_posted_at')->nullable()->index()->after('date');
                }
                if (!Schema::hasColumn('forum', 'last_poster_id')) {
                    $table->unsignedBigInteger('last_poster_id')->nullable()->index()->after('last_posted_at');
                }
                if (!Schema::hasColumn('forum', 'comments_count')) {
                    $table->unsignedInteger('comments_count')->default(0)->index()->after('reply');
                }
            });
        }

        // 3. Backfill default slugs, colors for existing categories
        $palette = ['#615dfa', '#00c7d9', '#e056fd', '#f0932b', '#22c55e', '#3b82f6', '#f43f5e'];
        $categories = DB::table('f_cat')->get();
        foreach ($categories as $index => $cat) {
            $color = $cat->color ?: ($palette[$index % count($palette)] ?? '#615dfa');
            $slug = $cat->slug ?: \Illuminate\Support\Str::slug($cat->name);
            if (!$slug) {
                $slug = 'category-' . $cat->id;
            }
            DB::table('f_cat')->where('id', $cat->id)->update([
                'color' => $color,
                'slug' => $slug,
            ]);
        }

        // 4. Backfill existing topics with last_posted_at, comments_count and slugs
        DB::table('forum')->whereNull('last_posted_at')->orWhere('last_posted_at', 0)->update([
            'last_posted_at' => DB::raw('`date`'),
            'last_poster_id' => DB::raw('`uid`'),
        ]);

        // Sync comments_count from f_coment
        $commentCounts = DB::table('f_coment')
            ->selectRaw('tid, COUNT(*) as c')
            ->groupBy('tid')
            ->get();
        foreach ($commentCounts as $row) {
            // Find latest comment for each topic
            $latestComment = DB::table('f_coment')->where('tid', $row->tid)->orderBy('id', 'desc')->first();
            DB::table('forum')->where('id', $row->tid)->update([
                'comments_count' => (int) $row->c,
                'last_posted_at' => $latestComment && $latestComment->date ? (int) $latestComment->date : DB::raw('`date`'),
                'last_poster_id' => $latestComment && $latestComment->uid ? (int) $latestComment->uid : DB::raw('`uid`'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('forum')) {
            Schema::table('forum', function (Blueprint $table) {
                if (Schema::hasColumn('forum', 'comments_count')) {
                    $table->dropColumn('comments_count');
                }
                if (Schema::hasColumn('forum', 'last_poster_id')) {
                    $table->dropColumn('last_poster_id');
                }
                if (Schema::hasColumn('forum', 'last_posted_at')) {
                    $table->dropColumn('last_posted_at');
                }
                if (Schema::hasColumn('forum', 'slug')) {
                    $table->dropColumn('slug');
                }
            });
        }

        if (Schema::hasTable('f_cat')) {
            Schema::table('f_cat', function (Blueprint $table) {
                if (Schema::hasColumn('f_cat', 'color')) {
                    $table->dropColumn('color');
                }
                if (Schema::hasColumn('f_cat', 'slug')) {
                    $table->dropColumn('slug');
                }
            });
        }
    }
};
