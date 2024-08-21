<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'name' => 'array',
        'prices' => 'array',
        'time_period' => 'array',
        'features' => 'array',
    ];
}
