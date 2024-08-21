<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingPayment extends Model
{
    use HasFactory;

    protected $table = 'matching_payment';

    protected $fillable = ['matching_id', 'stripe_response'];
    protected $casts = [
        'stripe_response' => 'array',
    ];

    public function matching()
    {
        return $this->belongsTo(Matching::class, 'matching_id');
    }
}
