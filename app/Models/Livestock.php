<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livestock extends Model
{

    use HasFactory;

    protected $fillable = [ 
      'create_product',
      'quantity',
      'units',
      'price_per_unit',
      'breed',
      'age',
      'feed_type',
      'health_status',
      'vaccination_status',
      'description',
      'farmer_id',
      'temp_min',
      'temp_max',
      'humidity_min',
      'humidity_max',
      'warehouse_id',
    ];
}