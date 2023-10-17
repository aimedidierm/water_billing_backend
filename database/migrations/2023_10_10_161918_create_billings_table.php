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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->unsignedBigInteger('reading_id');
            $table->foreign('reading_id')->on('meter_readings')->references('id')->onDelete("restrict");
            $table->enum('status', ['pending', 'payed'])->default('pending');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id')->onDelete("restrict");
            $table->unsignedBigInteger('meter_id');
            $table->foreign('meter_id')->on('meters')->references('id')->onDelete("restrict");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
