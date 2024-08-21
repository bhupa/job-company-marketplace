<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForceUpdate extends Model
{
    use HasFactory;

    protected $table = 'force_updates';
    protected $fillable = [
        'ios_version',
        'ios_build_number',
        'android_version',
        'android_build_number',
    ];
}
