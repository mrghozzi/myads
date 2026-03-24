<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_requests')) {
            Schema::create('order_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid');
                $table->string('title');
                $table->text('description');
                $table->string('budget')->nullable();
                $table->string('category')->nullable();
                $table->unsignedBigInteger('date'); // Using legacy timestamp format for consistency
                $table->integer('statu')->default(1); // 1 = open, 0 = closed
                $table->timestamps();

                $table->index('uid');
                $table->index('statu');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_requests');
    }
};
