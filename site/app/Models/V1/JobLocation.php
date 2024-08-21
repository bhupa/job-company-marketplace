<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobLocation extends Model
{
    use HasFactory;

    protected $table = 'job_locations';

    protected $fillable = ['job_id', 'district_id'];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
