<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JapaneseLevels extends Model
{
    use HasFactory;

    // protected $table='japanese_levels';

    // protected $fillable=[
    //     'level',
    //     'order'
    // ];
    protected static $levels = [
        ['label' => 'N1', 'value' => '1'],
        ['label' => 'N2', 'value' => '2'],
        ['label' => 'N3', 'value' => '3'],
        ['label' => 'N4', 'value' => '4'],
        ['label' => 'N5', 'value' => '5'],
    ];

    public static function getallLevels()
    {
        return collect(self::$levels);
    }
}
