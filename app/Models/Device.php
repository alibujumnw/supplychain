<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{

    use HasFactory, HasUuids;
    protected $fillable = [
        'name',
        'device_type',
        'serial_number',
        'description',
        'status',
        'user_id',
        'device_location',
        'location',
        'reg_number',
    ];
}
