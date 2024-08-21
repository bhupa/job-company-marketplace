<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matching extends Model
{
    use HasFactory;

    protected $table = 'matching';
    protected $fillable = [
        'company_id',
        'job_seeker_id',
        'job_id',
        'created_by',
        'favourite_by',
        'matched',
        'unmatched',
        'parent_id',
    ];

    protected $dates = ['matched', 'unmatched'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class, 'job_seeker_id');
    }

    public function job()
    {
        return $this->belongsTo(Jobs::class, 'job_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function favourite()
    {
        return $this->belongsTo(User::class, 'favourite_by');
    }

    // public function payment()
    // {
    //     return $this->hasOne(MatchingPayment::class, 'matching_id');
    // }
    // public function parent()
    // {
    //     return $this->belongsTo(Matching::class, 'parent_id');
    // }
    // public function child()
    // {
    //     return $this->hasOne(Matching::class, 'parent_id');
    // }
    public function room()
    {
        return $this->hasOne(ChatRoom::class, 'matching_id');
    }
}
