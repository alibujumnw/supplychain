<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logistic extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [ 
        'company_name', 
        'company_location', 
        'company_phone',
        'vihecle_type', 
        'vihecle_number',
        'driver',
        'driver_phone', 
        'logistic_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'logistic_id');
    }
}
