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
        // 1. Ensure developer_apps table and all required columns exist
        if (!Schema::hasTable('developer_apps')) {
            Schema::create('developer_apps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->string('domain');
                $table->text('description')->nullable();
                $table->string('logo')->nullable();
                $table->string('status')->default('draft');
                $table->string('client_id')->unique();
                $table->string('client_secret');
                $table->json('redirect_uris')->nullable();
                $table->json('requested_scopes')->nullable();
                $table->json('widget_capabilities')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('developer_apps', function (Blueprint $table) {
                if (!Schema::hasColumn('developer_apps', 'redirect_uris')) {
                    $table->json('redirect_uris')->nullable();
                }
                if (!Schema::hasColumn('developer_apps', 'requested_scopes')) {
                    $table->json('requested_scopes')->nullable();
                }
                if (!Schema::hasColumn('developer_apps', 'widget_capabilities')) {
                    $table->json('widget_capabilities')->nullable();
                }
                if (!Schema::hasColumn('developer_apps', 'client_secret')) {
                    $table->string('client_secret')->nullable();
                }
                if (!Schema::hasColumn('developer_apps', 'status')) {
                    $table->string('status')->default('draft');
                }
            });
        }

        // 2. Ensure developer_authorizations table and columns exist
        if (!Schema::hasTable('developer_authorizations')) {
            Schema::create('developer_authorizations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('developer_app_id');
                $table->json('scopes')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'developer_app_id']);
            });
        } else {
            Schema::table('developer_authorizations', function (Blueprint $table) {
                if (!Schema::hasColumn('developer_authorizations', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('id');
                }
                if (!Schema::hasColumn('developer_authorizations', 'developer_app_id')) {
                    $table->unsignedBigInteger('developer_app_id')->after('user_id');
                }
                if (!Schema::hasColumn('developer_authorizations', 'scopes')) {
                    $table->json('scopes')->nullable()->after('developer_app_id');
                }
            });
        }

        // 3. Ensure developer_authorization_codes table and all columns exist
        if (!Schema::hasTable('developer_authorization_codes')) {
            Schema::create('developer_authorization_codes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('developer_app_id');
                $table->unsignedBigInteger('user_id');
                $table->string('code')->unique();
                $table->text('redirect_uri');
                $table->json('scopes')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('used')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('developer_authorization_codes', function (Blueprint $table) {
                if (!Schema::hasColumn('developer_authorization_codes', 'developer_app_id')) {
                    $table->unsignedBigInteger('developer_app_id')->after('id');
                }
                if (!Schema::hasColumn('developer_authorization_codes', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('developer_app_id');
                }
                if (!Schema::hasColumn('developer_authorization_codes', 'code')) {
                    $table->string('code')->after('user_id');
                }
                if (!Schema::hasColumn('developer_authorization_codes', 'redirect_uri')) {
                    $table->text('redirect_uri')->after('code');
                }
                if (!Schema::hasColumn('developer_authorization_codes', 'scopes')) {
                    $table->json('scopes')->nullable()->after('redirect_uri');
                }
                if (!Schema::hasColumn('developer_authorization_codes', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('scopes');
                }
                if (!Schema::hasColumn('developer_authorization_codes', 'used')) {
                    $table->boolean('used')->default(false)->after('expires_at');
                }
            });
        }

        // 4. Ensure developer_access_tokens table and all columns exist
        if (!Schema::hasTable('developer_access_tokens')) {
            Schema::create('developer_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('developer_app_id');
                $table->unsignedBigInteger('user_id');
                $table->string('access_token')->unique();
                $table->json('scopes')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('revoked')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('developer_access_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('developer_access_tokens', 'developer_app_id')) {
                    $table->unsignedBigInteger('developer_app_id')->after('id');
                }
                if (!Schema::hasColumn('developer_access_tokens', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('developer_app_id');
                }
                if (!Schema::hasColumn('developer_access_tokens', 'access_token')) {
                    $table->string('access_token')->after('user_id');
                }
                if (!Schema::hasColumn('developer_access_tokens', 'scopes')) {
                    $table->json('scopes')->nullable()->after('access_token');
                }
                if (!Schema::hasColumn('developer_access_tokens', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('scopes');
                }
                if (!Schema::hasColumn('developer_access_tokens', 'revoked')) {
                    $table->boolean('revoked')->default(false)->after('expires_at');
                }
            });
        }

        // 5. Ensure developer_refresh_tokens table and all columns exist
        if (!Schema::hasTable('developer_refresh_tokens')) {
            Schema::create('developer_refresh_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('developer_access_token_id');
                $table->string('refresh_token')->unique();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('revoked')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('developer_refresh_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('developer_refresh_tokens', 'developer_access_token_id')) {
                    $table->unsignedBigInteger('developer_access_token_id')->after('id');
                }
                if (!Schema::hasColumn('developer_refresh_tokens', 'refresh_token')) {
                    $table->string('refresh_token')->after('developer_access_token_id');
                }
                if (!Schema::hasColumn('developer_refresh_tokens', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('refresh_token');
                }
                if (!Schema::hasColumn('developer_refresh_tokens', 'revoked')) {
                    $table->boolean('revoked')->default(false)->after('expires_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive rollback
    }
};
