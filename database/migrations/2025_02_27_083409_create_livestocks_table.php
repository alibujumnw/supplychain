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
        Schema::create('livestocks', function (Blueprint $table) {
            $table->id('id');
            $table->string('create_product');
            $table->string('quantity');
            $table->string('units');
            $table->string('price_per_unit');
            $table->string('breed');
            $table->string('age');
            $table->string('feed_type');
            $table->string('health_status');
            $table->string('vaccination_status');
            $table->string('description');
            $table->string('farmer_id');
            $table->string('temp_min');
            $table->string('temp_max');
            $table->string('humidity_min');
            $table->string('humidity_max');
            $table->string('warehouse_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestocks');
    }
};