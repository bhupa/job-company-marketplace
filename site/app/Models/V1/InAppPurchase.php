<?php

namespace App\Models\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InAppPurchase extends Model
{
    use HasFactory;

    protected $table = 'in_app_purchases';

    protected $fillable = [
        'user_id',
        'item_id',
        'status', // 'pending,cancel,active,expired,trial',
        'purchase_token',
        'payment_type', // 'google' | 'apple'
        'payment_for',
        'transaction_receipt',
        'trial_ends_at',
        'ends_at',
        'currency',
        'price',
        'store_user_id',
        'order_id', // transactionId in app store google pay order_id
    ];

    protected $dates = ['trial_ends_at', 'ends_at'];
    protected $casts = [
        'ends_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getPlanAttribute()
    {
        if (env('APP_ENV') == 'production') {
            $lists = iapListProd($this->user);
        } elseif (env('APP_ENV') == 'staging') {
            $lists = iapListStagging($this->user);
        } else {
            $lists = iapLists($this->user);
        }
        $chat = Str::contains($this->item_id, 'super_chat');
        if ($chat) {
            if ($this->payment_type == 'apple') {
                return $lists['ios']['superChat'][$this->item_id];
            } else {
                return $lists['android']['superChat'][$this->item_id];
            }
        } else {
            if (
                $this->payment_type == 'apple'
                || $this->payment_type == 'trial'
            ) {
                return $lists['ios']['subscription'][$this->item_id];
            } else {
                return $lists['android']['subscription'][$this->item_id];
            }
        }
    }
}
