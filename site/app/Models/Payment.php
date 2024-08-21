<?php

namespace App\Models;

use App\Models\V1\Esewa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'type', // which payment is done for now only chat
        'payment_intent_id',
        'status',
        'user_id',
        'model_id',
        'model',
    ];

    public function esewa()
    {
        return $this->belongsTo(Esewa::class, 'payment_intent_id')->where('type', 'super_chat');
    }
}
