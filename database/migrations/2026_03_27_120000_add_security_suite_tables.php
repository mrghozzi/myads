<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'public_uid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('public_uid', 24)->nullable()->after('username');
                $table->unique('public_uid', 'users_public_uid_unique');
            });

            try {
                DB::table('users')
                    ->select('id')
                    ->orderBy('id')
                    ->chunkById(100, function ($users) {
                        foreach ($users as $user) {
                            DB::table('users')
                                ->where('id', $user->id)
                                ->whereNull('public_uid')
                                ->update([
                                    'public_uid' => $this->newPublicUid(),
                                ]);
                        }
                    });
            } catch (\Throwable) {
                // Ignore backfill failures on partially upgraded installs.
            }
        }

        if (!Schema::hasTable('security_ip_bans')) {
            Schema::create('security_ip_bans', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('ip_address', 45)->index();
                $table->string('reason', 255)->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedBigInteger('banned_by')->nullable()->index();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('security_member_sessions')) {
            Schema::create('security_member_sessions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('session_id', 191)->unique();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('started_via', 32)->default('web');
                $table->string('ip_address', 45)->nullable()->index();
                $table->string('user_agent', 1000)->nullable();
                $table->timestamp('started_at')->nullable()->index();
                $table->timestamp('last_seen_at')->nullable()->index();
                $table->timestamp('ended_at')->nullable()->index();
                $table->timestamp('revoked_at')->nullable()->index();
                $table->unsignedBigInteger('revoked_by')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('security_member_sessions')) {
            Schema::drop('security_member_sessions');
        }

        if (Schema::hasTable('security_ip_bans')) {
            Schema::drop('security_ip_bans');
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'public_uid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_public_uid_unique');
                $table->dropColumn('public_uid');
            });
        }
    }

    private function newPublicUid(): string
    {
        do {
            $candidate = strtoupper(Str::random(12));
        } while (DB::table('users')->where('public_uid', $candidate)->exists());

        return $candidate;
    }
};
