<?php

namespace App\Models;

use App\Models\V1\InAppPurchase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_room';

    protected $fillable = [
        'name',
        'image',
        'admin_assist',
        'matching_id',
        'created_by',
        'type',
        'user_id',
        'payment_id',
        'status',
        'payment_type',
        'in_app_id',
    ];

    public function match()
    {
        return $this->belongsTo(Matching::class, 'matching_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'chat_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id')->where('type', '!=', 'esewa');
    }

    public function esewa()
    {
        return $this->belongsTo(Payment::class, 'payment_id')->where('type', 'esewa');
    }

    public function violation()
    {
        return $this->hasOne(ViolationReports::class, 'chat_room_id');
    }

    public function getMatchedUserAttribute($user)
    {
        if (!empty($this->matching_id)) {
            if ($user->user_type == 2) {
                if (
                    $this->match->jobseeker
                    && $this->match->jobseeker->user_id != $user->id
                ) {
                    return [
                        'name' => $this->match->jobseeker
                            ? $this->match->jobseeker->first_name.
                                ' '.
                                $this->match->jobseeker->last_name
                            : null,
                        'userId' => $this->match->jobseeker->user_id,
                        'jobseekerId' => $this->match->jobseeker->id,
                        'companyId' => null,
                        'image' => $this->match->jobseeker->profile_img
                            ? url('/').
                                '/storage/'.
                                $this->match->jobseeker->profile_img
                            : 'https://fastly.picsum.photos/id/0/5000/3333.jpg?hmac=_j6ghY5fCfSD6tvtcV74zXivkJSPIfR9B8w34XeQmvU',
                    ];
                }
            } else {
                if (
                    $this->match->company
                    && $this->match->company->user_id != $user->id
                ) {
                    return [
                        'name' => $this->match->company->company_name,
                        'userId' => $this->match->company->user_id,
                        'jobseekerId' => null,
                        'companyId' => $this->match->company->id,
                        'image' => $this->match->company->logo
                            ? url('/').
                                '/storage/'.
                                $this->match->company->logo
                            : 'https://fastly.picsum.photos/id/0/5000/3333.jpg?hmac=_j6ghY5fCfSD6tvtcV74zXivkJSPIfR9B8w34XeQmvU',
                    ];
                }
            }
        }

        return null;
    }

    public function scopeHasChatWithViolationStatus()
    {
        return $this->whereHas('chats.violation', function ($violationQuery) {
            $violationQuery->where('status', 1);
        });
    }

    public function violations()
    {
        return $this->morphToMany(Violation::class, 'violatable');
    }

    public function iap()
    {
        return $this->hasOne(InAppPurchase::class, 'in_app_id')->where(
            'type',
            'super_chat'
        );
    }

    public function getSuperChatAttribute()
    {
        if ($this->payment_type == 'iap') {
            return $this->iap();
        } elseif ($this->payment_type == 'stripe') {
            return $this->payment();
        } elseif ($this->payment_type == 'admin') {
            return true;
        } elseif ($this->payment_type == 'esewa') {
            return $this->esewa();
        }

        return null;
    }

    public function getlastSeenIdAttribute()
    {
        return $this->chats()
            ->where('send_by', '!=', auth()->id())
            ->whereNotNull('seen')
            ->latest()
            ->value('id');
    }

    public function getsenderDetailsAttribute()
    {
        return $this->chats()
            ->where('send_by', '!=', auth()->id())
            ->latest()
            ->value('send_by');
    }
}
