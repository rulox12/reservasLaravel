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
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('room_number');
            $table->enum('type', ['single', 'double', 'suite']);
            $table->decimal('price', 8, 2);
            $table->enum('status', ['available', 'reserved', 'occupied'])->default('available');
            $table->enum('capacity', ['2', '4', '8'])->default('2');
            $table->enum('climate_control', ['fan', 'air_conditioning'])->default('fan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
