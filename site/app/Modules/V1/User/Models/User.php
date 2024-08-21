<?php

namespace App\Modules\V1\User\Models;

use App\Models\Company;
use App\Models\Flip;
use App\Models\ImageFiles;
use App\Models\Jobs;
use App\Models\Jobseeker;
use App\Models\Matching;
use App\Models\V1\Esewa;
use App\Models\V1\InAppPurchase;
use App\Models\V1\ReasonForCancellation;
use App\Models\ViolationReports;
use App\Modules\V1\Auth\Notifications\EmailVerifyNotification;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'country_code',
        'phone',
        'email',
        'password',
        'user_type',
        'status',
        'verification_token',
        'device_token',
        'google_id',
        'facebook_id',
        'apple_id',
        'otp',
        'comment',
        'is_verify',
        'email_verified_at',
        'intro_video',
        'subscriptions_type', // iap stripe to check from where user have make payment iap,stripe,trial,admin-pay
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sendPasswordResetNotification($token)
    {
        $url = url('/').'reset-password?token='.$token;

        $this->notify(new ResetPasswordNotification($url));
    }

    public function verificationUrl()
    {
        return route('verification.notice', [
            'id' => $this->id,
            'hash' => Str::random(60),
        ]);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerifyNotification($this, $this->verificationUrl()));
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    public function jobseeker()
    {
        return $this->hasOne(Jobseeker::class, 'user_id');
    }

    public function jobs()
    {
        return $this->hasMany(Jobs::class);
    }

    public function flips()
    {
        return $this->hasMany(Flip::class, 'user_id');
    }

    public function matching()
    {
        return $this->hasMany(Matching::class, 'created_by');
    }

    public function documents()
    {
        return $this->hasMany(ImageFiles::class, 'model_id')
            ->where('model', self::class)
            ->where('type', 'user-verify');
    }

    public function iap()
    {
        return $this->hasMany(InAppPurchase::class, 'user_id')
            ->where('payment_for', 'subscription')
            ->where('ends_at', '>', Carbon::now())
            ->latest()
            ->first();
    }

    public function esewa()
    {
        return $this->hasMany(Esewa::class, 'user_id')
            ->where('type', 'subscription')
            ->where('ends_at', '>', Carbon::now())
            ->latest()
            ->first();
    }

    public function getFullNameAttribute()
    {
        if ($this->user_type == 1) {
            return $this->jobseeker
                ? $this->jobseeker->first_name.
                ' '.
                $this->jobseeker->last_name
                : null;
        } elseif ($this->user_type == 2) {
            return $this->company ? $this->company->company_name : null;
        }
    }

    public function getVetificationAttribute()
    {
        $verify =
            $this->user_type == 1
            ? ($this->jobseeker
                ? $this->jobseeker->is_verify
                : null)
            : ($this->company
                ? $this->company->is_verify
                : null);
        if (
            empty($this->documents)
            && empty($this->comment)
            && empty($verify)
        ) {
            return null;
        } elseif (
            $this->documents()->count() > 0
            && empty($this->comment)
            && empty($verify)
        ) {
            return 'PENDING';
        } elseif (
            $this->documents()->count() > 0
            && !empty($this->comment)
            && $verify != 1
        ) {
            return 'REJECTED';
        } elseif (
            $this->documents()->count() > 0
            && empty($this->comment)
            && $verify == 1
        ) {
            return 'APPROVED';
        }
    }

    public function violation()
    {
        return $this->hasOne(ViolationReports::class, 'user_id');
    }

    public function getSubscribedTypeAttribute()
    {
        if (
            $this->subscriptions_type == 'iap'
            || $this->subscriptions_type == 'trial'
            || $this->subscriptions_type == 'admin-pay'
        ) {
            return $this->iap() ?? null;
        } elseif ($this->subscriptions_type == 'stripe') {
            return $this->subscriptions()
                ->where('ends_at', '>', Carbon::now())
                ->latest('created_at')
                ->first();
        } elseif ($this->subscriptions_type == 'esewa') {
            return $this->esewa() ?? null;
        }
    }

    public function cancellationReason()
    {
        return $this->hasOne(ReasonForCancellation::class, 'user_id');
    }
}
