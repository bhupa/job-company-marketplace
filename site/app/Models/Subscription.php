<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'stripe_id',
        'name',
        'stripe_status',
        'stripe_price',
        'trials_end_at',
        'ends_at',
        'quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function item()
    {
        return $this->hasOne(SubscriptionItem::class, 'subscription_id');
    }
}
