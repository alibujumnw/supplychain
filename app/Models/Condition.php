<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Condition extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [ 
        'temperature', 
        'warehouse_id', 
        'humidity',  
        'recorded_at',
    ];
}
