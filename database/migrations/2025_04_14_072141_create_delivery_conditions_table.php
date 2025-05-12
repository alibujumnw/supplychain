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
        Schema::create('delivery_conditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('temperature');  
            $table->text('vehicle_id');  
            $table->float('humidity');  
            $table->text('farmer_id');
            $table->dateTime('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_conditions');
    }
};
