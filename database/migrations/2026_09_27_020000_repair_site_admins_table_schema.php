<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('site_admins')) {
            Schema::create('site_admins', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->boolean('is_super')->default(false);
                $table->boolean('has_full_access')->default(false);
                $table->json('permissions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('created_by');
            });
        } else {
            Schema::table('site_admins', function (Blueprint $table) {
                if (!Schema::hasColumn('site_admins', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('site_admins', 'is_super')) {
                    $table->boolean('is_super')->default(false)->after('user_id');
                }
                if (!Schema::hasColumn('site_admins', 'has_full_access')) {
                    $table->boolean('has_full_access')->default(false)->after('is_super');
                }
                if (!Schema::hasColumn('site_admins', 'permissions')) {
                    $table->json('permissions')->nullable()->after('has_full_access');
                }
                if (!Schema::hasColumn('site_admins', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('permissions');
                }
                if (!Schema::hasColumn('site_admins', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('site_admins', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->after('created_by');
                }
                if (!Schema::hasColumn('site_admins', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }

        // Seed super admin user #1 if users table exists and user #1 is present
        if (Schema::hasTable('users') && Schema::hasTable('site_admins') && Schema::hasColumn('site_admins', 'user_id')) {
            try {
                if (DB::table('users')->where('id', 1)->exists()) {
                    $attributes = ['user_id' => 1];
                    $values = [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('site_admins', 'is_super')) {
                        $values['is_super'] = true;
                    }
                    if (Schema::hasColumn('site_admins', 'has_full_access')) {
                        $values['has_full_access'] = true;
                    }
                    if (Schema::hasColumn('site_admins', 'permissions')) {
                        $values['permissions'] = json_encode(['all']);
                    }
                    if (Schema::hasColumn('site_admins', 'is_active')) {
                        $values['is_active'] = true;
                    }
                    if (Schema::hasColumn('site_admins', 'created_by')) {
                        $values['created_by'] = 1;
                    }

                    DB::table('site_admins')->updateOrInsert($attributes, $values);
                }
            } catch (\Throwable) {
                // Graceful fallback if any constraint issues occur
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive repair migration.
    }
};
