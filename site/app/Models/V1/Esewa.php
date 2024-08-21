<?php

namespace App\Models\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Esewa extends Model
{
    use HasFactory;

    protected $table = 'esewas';
    protected $fillable = [
        'user_id',
        'signature',
        'transaction_code',
        'transaction_uuid',
        'product_code',
        'price_id',
        'status',
        'type',
        'ends_at',
        'payment_form',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
