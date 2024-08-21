<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company';
    protected $fillable = [
        'company_name',
        'about_company',
        'about_company_ja',
        'address',
        'status',
        'user_id',
        'logo',
        'is_verify',
    ];

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

    public function jobs()
    {
        return $this->hasMany(Jobs::class, 'user_id', 'user_id');
    }

    public function matching()
    {
        return $this->hasMany(Matching::class, 'company_id');
    }

    public function getProfilePercentageAttribute()
    {
        $verifed = $this->is_verify ? 30 : 0;
        $image = $this->images ? 10 : 0;
        $logo = $this->logo ? 10 : 0;
        $validation = 50;
        $profilePercentage = round($validation + $verifed + $image + $logo, 2);

        return $profilePercentage;
    }

    public function getFullNameAttribute()
    {
        return $this->company_name;
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
}
