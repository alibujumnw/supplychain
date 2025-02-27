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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('quantity');
            $table->string('kilograms');
            $table->string('price_per_unit');
            $table->string('planting_date');
            $table->string('expected_harvest_data');
            $table->string('soil_type');
            $table->string('irrigation-method');
            $table->string('fertilizers_used');
            $table->string('description');
            $table->string('farmer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
