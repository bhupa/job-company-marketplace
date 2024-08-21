<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCheck extends Model
{
    use HasFactory;

    protected $table = 'otp_check';

    protected $fillable = ['device_token'];
}
