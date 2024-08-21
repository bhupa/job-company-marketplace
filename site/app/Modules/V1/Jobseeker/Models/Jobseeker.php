<?php

namespace App\Modules\V1\Jobseeker\Models;

use App\Models\ImageFiles;
use App\Models\JobCategory;
use App\Models\Matching;
use App\Models\V1\Tag;
use App\Modules\V1\Jobseeker\Traits\JobTraits;
use App\Modules\V1\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Jobseeker extends Model
{
    // Define your model properties and methods here

    use JobTraits;

    protected $table = 'jobseekers';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'birthday',
        'gender', // male = 1 , female = 2, binary = 3
        'country',
        'current_country',
        'occupation',
        'experience', // less than 1 year = 1, less than 2 year =2, less than 3 year = 3, 3 or more = 4
        'japanese_level', // N1 = 1 , N2 = 2, N3 = 3, N4 = 4 , N5 =5
        'about',
        'job_type',
        'about_ja',
        'start_when',
    ];
    protected $dates = ['birthday'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ImageFiles::class, 'model_id')->where(
            'model',
            self::class
        );
    }

    public function matching()
    {
        return $this->hasMany(Matching::class, 'job_seeker_id');
    }

    public function occupations()
    {
        return $this->belongsTo(JobCategory::class, 'occupation');
    }

    public function getProfilePercentageAttribute()
    {
        $verified = $this->is_verify ? 30 : 0;
        $userInput = [
            'current_country',
            'occupation',
            'experience',
            'job_type',
        ];

        $i = 0;
        foreach ($userInput as $input) {
            if ($this->$input) {
                ++$i;
            }
        }
        $validation = 30;
        $profilePercentage = round($validation + $verified + ($i / 4) * 40, 2);

        return $profilePercentage;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.''.$this->last_name;
    }

    public function getVetificationAttribute()
    {
        if (
            empty($this->user->documents)
            && empty($this->user->comment)
            && empty($this->verify)
        ) {
            return null;
        } elseif (
            $this->user->documents
            && empty($this->user->comment)
            && empty($this->verify)
        ) {
            return 'PENDING';
        } elseif (
            $this->user->documents
            && $this->user->comment
            && empty($this->verify)
        ) {
            return 'REJECTED';
        } elseif (
            $this->user->documents
            && empty($this->user->comment)
            && $this->verify
        ) {
            return 'APPROVED';
        }
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'user_tags',
            'jobseeker_id',
            'tag_id'
        );
    }
}
