<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mail_mailer', 20)->default('smtp');
            $table->string('mail_host', 255)->nullable();
            $table->unsignedSmallInteger('mail_port')->nullable();
            $table->string('mail_username', 255)->nullable();
            $table->text('mail_password')->nullable()->comment('Encrypted');
            $table->string('mail_encryption', 10)->nullable();
            $table->string('mail_from_address', 255)->nullable();
            $table->string('mail_from_name', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
