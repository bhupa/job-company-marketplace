<?php

namespace App\Models\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonForCancellation extends Model
{
    use HasFactory;
    protected $table = 'reason_for_cancellation';
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'user_type',
        'reason',
        'sub_reason',
        'future_plan',
        'comment',
    ];

   
}
