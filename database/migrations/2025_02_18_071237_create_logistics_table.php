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
        Schema::create('logistics', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('company_name'); 
        $table->string('company_location'); 
        $table->string('company_phone');
        $table->string('vehicle_type'); 
        $table->string('vehicle_number');
        $table->string('driver_full_name');
        $table->string('driver_phone');
        $table->string('logistic_id');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistics');
    }
};
