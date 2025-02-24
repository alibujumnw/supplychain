<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory,HasUuids;
    
    protected $fillable = [ 
        'surname',
        'name',
        'company_name',
        'company_address',
        'phone_number',
        'supplier_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
}
