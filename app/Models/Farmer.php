<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farmer extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [ 
        'surname',
        'name',
        'farm_name', 
        'farm_location', 
        'farm_size', 
        'farmer_id' 
    ];
}
