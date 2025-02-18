<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensorData extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [ 
        'temperature', 
        'soil_moisture', 
        'humidity', 
        'rainfall', 
        'recorded_at',
    ];
}
