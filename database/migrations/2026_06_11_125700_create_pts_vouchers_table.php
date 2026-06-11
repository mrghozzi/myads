<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pts_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // the generator
            $table->string('code', 50)->unique();
            $table->double('amount');
            $table->boolean('is_used')->default(false);
            $table->unsignedBigInteger('used_by')->nullable(); // the claimer
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            // Note: Since this project relies heavily on MyISAM, foreign keys might be ignored depending on engine.
            // But we can add them for logic structure.
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('used_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pts_vouchers');
    }
};
