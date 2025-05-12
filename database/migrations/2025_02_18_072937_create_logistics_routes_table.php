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
        Schema::create('logistics_routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('route_name')->nullable();
            $table->string('device_type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('start_location')->nullable();
            $table->string('end_location')->nullable();
            $table->string('est_time')->nullable();
            $table->string('device')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistics_routes');
    }
};
