<?php

namespace App\Models;

use App\Models\V1\Tag;
use App\Traits\JobTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobseeker extends Model
{
    use HasFactory;
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
        'living_japan',
        'is_verify',
        'profile_img',
        'job_type',
        'longterm',
        'about_ja',
        'start_when',
        'intro_video',
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
            'first_name',
            'last_name',
            'image',
            'profile_img',
            'birthday',
            'gender',
            'country',
            'current_country',
            'occupation',
            'job_type',
            'experience',
            'japanese_level',
            'about',
            'about_ja',
            'start_when',
            'intro_video',
        ];

        // count no of elements inside userInput
        $numberOfElements = count($userInput);

        $i = 0;
        foreach ($userInput as $key => $input) {
            if ($this->$input) {
                ++$i;
            }
        }
        $profilePercentage = round($verified + ($i / $numberOfElements) * 70, 2);

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
