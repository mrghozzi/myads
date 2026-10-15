<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modernize directory table
        if (Schema::hasTable('directory')) {
            Schema::table('directory', function (Blueprint $table) {
                if (!Schema::hasColumn('directory', 'slug')) {
                    $table->string('slug', 255)->nullable()->after('name');
                }
                if (!Schema::hasColumn('directory', 'pricing_type')) {
                    $table->string('pricing_type', 30)->default('free')->after('cat');
                }
                if (!Schema::hasColumn('directory', 'upvotes_count')) {
                    $table->integer('upvotes_count')->default(0)->after('vu');
                }
                if (!Schema::hasColumn('directory', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('upvotes_count');
                }
                if (!Schema::hasColumn('directory', 'is_verified')) {
                    $table->boolean('is_verified')->default(false)->after('is_featured');
                }
                if (!Schema::hasColumn('directory', 'claimed_by')) {
                    $table->unsignedBigInteger('claimed_by')->nullable()->after('is_verified');
                }
                if (!Schema::hasColumn('directory', 'features')) {
                    $table->text('features')->nullable()->after('txt');
                }
                if (!Schema::hasColumn('directory', 'status_code')) {
                    $table->smallInteger('status_code')->nullable()->after('statu');
                }
                if (!Schema::hasColumn('directory', 'last_checked_at')) {
                    $table->bigInteger('last_checked_at')->nullable()->after('status_code');
                }
            });

            // Add indexes safely
            try {
                Schema::table('directory', function (Blueprint $table) {
                    $table->index('pricing_type');
                    $table->index('is_featured');
                    $table->index('upvotes_count');
                    $table->index('slug');
                });
            } catch (\Throwable $e) {
                // Ignore if index already exists
            }
        }

        // 2. Modernize cat_dir table
        if (Schema::hasTable('cat_dir')) {
            Schema::table('cat_dir', function (Blueprint $table) {
                if (!Schema::hasColumn('cat_dir', 'slug')) {
                    $table->string('slug', 255)->nullable()->after('name');
                }
                if (!Schema::hasColumn('cat_dir', 'icon')) {
                    $table->string('icon', 100)->nullable()->after('slug');
                }
            });

            try {
                Schema::table('cat_dir', function (Blueprint $table) {
                    $table->index('slug');
                });
            } catch (\Throwable $e) {
                // Ignore if index exists
            }
        }

        // 3. Create directory_upvotes table
        if (!Schema::hasTable('directory_upvotes')) {
            Schema::create('directory_upvotes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('directory_id');
                $table->unsignedBigInteger('user_id');
                $table->bigInteger('created_at');

                $table->index('directory_id');
                $table->index('user_id');
                $table->unique(['directory_id', 'user_id']);
            });
        }

        // 4. Create directory_claims table
        if (!Schema::hasTable('directory_claims')) {
            Schema::create('directory_claims', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('directory_id');
                $table->unsignedBigInteger('user_id');
                $table->string('verification_method', 50)->default('meta_tag');
                $table->string('verification_token', 100);
                $table->string('status', 30)->default('pending'); // pending, approved, rejected
                $table->text('notes')->nullable();
                $table->bigInteger('created_at');
                $table->bigInteger('updated_at')->nullable();

                $table->index('directory_id');
                $table->index('user_id');
                $table->index('status');
            });
        }

        // 5. Backfill slugs for existing records where slug is null
        try {
            if (Schema::hasTable('directory')) {
                $directories = DB::table('directory')->whereNull('slug')->orWhere('slug', '')->get(['id', 'name']);
                foreach ($directories as $dir) {
                    $slug = Str::slug($dir->name) ?: ('site-' . $dir->id);
                    DB::table('directory')->where('id', $dir->id)->update(['slug' => $slug]);
                }
            }

            if (Schema::hasTable('cat_dir')) {
                $categories = DB::table('cat_dir')->whereNull('slug')->orWhere('slug', '')->get(['id', 'name']);
                foreach ($categories as $cat) {
                    $slug = Str::slug($cat->name) ?: ('cat-' . $cat->id);
                    DB::table('cat_dir')->where('id', $cat->id)->update(['slug' => $slug]);
                }
            }
        } catch (\Throwable $e) {
            // Silently continue if backfill fails
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directory_claims');
        Schema::dropIfExists('directory_upvotes');

        if (Schema::hasTable('cat_dir')) {
            Schema::table('cat_dir', function (Blueprint $table) {
                if (Schema::hasColumn('cat_dir', 'icon')) {
                    $table->dropColumn('icon');
                }
                if (Schema::hasColumn('cat_dir', 'slug')) {
                    $table->dropColumn('slug');
                }
            });
        }

        if (Schema::hasTable('directory')) {
            Schema::table('directory', function (Blueprint $table) {
                $columns = ['slug', 'pricing_type', 'upvotes_count', 'is_featured', 'is_verified', 'claimed_by', 'features', 'status_code', 'last_checked_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('directory', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
