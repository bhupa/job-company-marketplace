<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $filable = [
        'name',
        'slug',
        'price',
        'stripe_plan',
        'description',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
