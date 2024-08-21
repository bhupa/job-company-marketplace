<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageFiles extends Model
{
    use HasFactory;

    protected $table = 'image_files';

    protected $fillable = ['image', 'model_id', 'model', 'type', 'file_type'];

    public function related()
    {
        return $this->morphTo('model', 'model_id', 'id');
    }
}
