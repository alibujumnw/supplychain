<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogisticsRoute extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'route_name',
        'device_type',
        'serial_number',
        'start_location',
        'end_location',
        'est_time',
        'device',
        'status',
    ];
}
