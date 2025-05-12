<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryCondition extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [ 
    'temperature', 
    'vehicle_id', 
    'humidity',  
    'farner_id',
    'recorded_at',
    ];
}
