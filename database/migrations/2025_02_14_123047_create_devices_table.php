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
        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable(); 
            $table->string('device_type')->nullable(); 
            $table->string('serial_number')->unique();
            $table->text('description')->nullable(); 
            $table->string('status')->default('inactive');
            $table->string('user_id')->nullable();
        $table->string('device_location');
        $table->string('location');
        $table->string('reg_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
