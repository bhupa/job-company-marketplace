<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flip extends Model
{
    use HasFactory;
    protected $table = 'flip_count';
    protected $fillable = ['user_id', 'flip', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
