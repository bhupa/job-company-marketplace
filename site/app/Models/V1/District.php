<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $table = 'districts';

    protected $fillable = ['name', 'status', 'ja_name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(District::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasMany(District::class, 'parent_id');
    }
}
