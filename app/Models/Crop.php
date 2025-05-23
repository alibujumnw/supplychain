<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crop extends Model
{

    use HasFactory;
    protected $fillable = [
   'product_name',
   'quantity',
   'kilograms',
   'price_per_unit',
   'storage_date',
   'storage_last_date',
   'soil_type',
   'irrigation-method',
    'fertilizers_used',
    'description',
    'farmer_id',
    'temp_min',
    'temp_max',
    'humidity_min',
    'humidity_max',
     'shelf_life',
     'warehouse_id'
    ];
}