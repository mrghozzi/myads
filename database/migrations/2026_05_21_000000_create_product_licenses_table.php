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
        Schema::create('product_licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // buyer
            $table->unsignedBigInteger('product_id'); // product purchased
            $table->string('license_key', 255)->unique();
            $table->string('domain', 255)->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            // Foreign keys / indices
            $table->index('user_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_licenses');
    }
};
