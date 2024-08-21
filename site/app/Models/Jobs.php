<?php

namespace App\Models;

use App\Models\V1\District;
use App\Models\V1\Tag;
use App\Traits\JobTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    use HasFactory;
    use JobTraits;

    protected $table = 'jobs';

    protected $fillable = [
        'job_title',
        'job_title_ja',
        'user_id',
        'salary_from',
        'salary_to',
        'gender',
        'occupation',
        'experience',
        'japanese_level',
        'required_skills',
        'required_skills_ja',
        'from_when',
        'published',
        'status',
        'job_type',
        'pay_type',
    ];

    protected $casts = [
        'holidays' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function occupations()
    {
        return $this->belongsTo(JobCategory::class, 'occupation');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'user_id');
    }

    public function matches()
    {
        return $this->hasMany(Matching::class, 'job_id');
    }

    public function scopeWithValidUser($query)
    {
        return $query->whereHas('user');
    }

    public function scopeWithValidCompany($query)
    {
        return $query->whereHas('user.company');
    }

    public function image()
    {
        return $this->hasOne(ImageFiles::class, 'model_id')->where(
            'model',
            self::class
        );
    }

    public function locations()
    {
        return $this->belongsToMany(
            District::class,
            'job_locations',
            'job_id',
            'district_id'
        );
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'user_tags',
            'job_id',
            'tag_id'
        );
    }
}
