<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'warehouse_name',
        'warehouse_size',
        'temp_min',
        'temp_max',
        'IoT_device_id',
        'warehouse_type',
     ];
}
