<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'produce_name',
        'status',
        'estimated_delivery_time',
        'actual_delivery_time',
    ];
}
