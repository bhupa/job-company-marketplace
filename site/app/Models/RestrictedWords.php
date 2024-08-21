<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestrictedWords extends Model
{
    use HasFactory;

    protected $table = 'restricted_words';
    protected $fillable = ['word'];
}
