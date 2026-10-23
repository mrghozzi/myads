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
        // 1. Add PKCE columns to developer_authorization_codes if not present
        if (Schema::hasTable('developer_authorization_codes')) {
            Schema::table('developer_authorization_codes', function (Blueprint $table) {
                if (!Schema::hasColumn('developer_authorization_codes', 'code_challenge')) {
                    $table->string('code_challenge', 128)->nullable()->after('code');
                }
                if (!Schema::hasColumn('developer_authorization_codes', 'code_challenge_method')) {
                    $table->string('code_challenge_method', 10)->nullable()->after('code_challenge');
                }
            });
        }

        // 2. Add App directory and public/confidential flags to developer_apps
        if (Schema::hasTable('developer_apps')) {
            Schema::table('developer_apps', function (Blueprint $table) {
                if (!Schema::hasColumn('developer_apps', 'app_type')) {
                    $table->string('app_type', 30)->default('confidential')->after('status');
                }
                if (!Schema::hasColumn('developer_apps', 'public_listed')) {
                    $table->boolean('public_listed')->default(false)->after('app_type');
                }
                if (!Schema::hasColumn('developer_apps', 'category')) {
                    $table->string('category', 60)->nullable()->after('public_listed');
                }
            });
        }

        // 3. Create developer_webhooks table
        if (!Schema::hasTable('developer_webhooks')) {
            Schema::create('developer_webhooks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('developer_app_id')->index();
                $table->string('url', 1000);
                $table->string('secret', 64);
                $table->json('events')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('failure_count')->default(0);
                $table->timestamps();
            });
        }

        // 4. Create developer_webhook_deliveries table
        if (!Schema::hasTable('developer_webhook_deliveries')) {
            Schema::create('developer_webhook_deliveries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('developer_webhook_id')->index();
                $table->unsignedBigInteger('developer_app_id')->index();
                $table->string('event', 100)->index();
                $table->json('payload')->nullable();
                $table->integer('status_code')->nullable();
                $table->text('response_body')->nullable();
                $table->integer('duration_ms')->default(0);
                $table->integer('attempts')->default(1);
                $table->text('error_message')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();
            });
        }

        // 5. Create developer_api_logs table
        if (!Schema::hasTable('developer_api_logs')) {
            Schema::create('developer_api_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('developer_app_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('token_type', 20)->default('oauth');
                $table->string('endpoint', 255)->index();
                $table->string('method', 10);
                $table->integer('status_code')->index();
                $table->integer('duration_ms')->default(0);
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->nullable()->index();
            });
        }

        // 6. Create developer_personal_tokens table
        if (!Schema::hasTable('developer_personal_tokens')) {
            Schema::create('developer_personal_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('name', 191);
                $table->string('token', 64)->unique();
                $table->json('scopes')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollback
        Schema::dropIfExists('developer_personal_tokens');
        Schema::dropIfExists('developer_api_logs');
        Schema::dropIfExists('developer_webhook_deliveries');
        Schema::dropIfExists('developer_webhooks');
    }
};
