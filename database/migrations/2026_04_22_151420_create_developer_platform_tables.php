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
        Schema::create('developer_apps', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->string('domain')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('status')->default('draft'); // draft, pending_review, active, rejected, suspended
            $table->string('client_id')->unique();
            $table->string('client_secret')->nullable();
            $table->json('redirect_uris')->nullable();
            $table->json('requested_scopes')->nullable();
            $table->json('widget_capabilities')->nullable();
            $table->timestamps();

        });

        Schema::create('developer_authorizations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->unsignedBigInteger('developer_app_id');
            $table->json('scopes')->nullable();
            $table->timestamps();

            $table->foreign('developer_app_id')->references('id')->on('developer_apps')->onDelete('cascade');
            $table->unique(['user_id', 'developer_app_id']);
        });

        Schema::create('developer_authorization_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('developer_app_id');
            $table->integer('user_id');
            $table->string('code')->unique();
            $table->string('redirect_uri');
            $table->json('scopes')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();

            $table->foreign('developer_app_id')->references('id')->on('developer_apps')->onDelete('cascade');
        });

        Schema::create('developer_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('developer_app_id');
            $table->integer('user_id');
            $table->string('access_token')->unique(); // Typically hashed in DB
            $table->json('scopes')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('revoked')->default(false);
            $table->timestamps();

            $table->foreign('developer_app_id')->references('id')->on('developer_apps')->onDelete('cascade');
        });

        Schema::create('developer_refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('developer_access_token_id');
            $table->string('refresh_token')->unique(); // Typically hashed in DB
            $table->timestamp('expires_at');
            $table->boolean('revoked')->default(false);
            $table->timestamps();

            $table->foreign('developer_access_token_id', 'fk_dev_refresh_tokens_access_id')
                  ->references('id')->on('developer_access_tokens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_refresh_tokens');
        Schema::dropIfExists('developer_access_tokens');
        Schema::dropIfExists('developer_authorization_codes');
        Schema::dropIfExists('developer_authorizations');
        Schema::dropIfExists('developer_apps');
    }
};
