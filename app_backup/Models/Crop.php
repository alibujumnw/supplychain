<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crop extends Model
{

    use HasFactory, HasUuids;
    protected $fillable = [
    'crop_type',
    'harvest_timeline',
    'quantity',
    'quality'
    ];
}
