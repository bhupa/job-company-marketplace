<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDeletionGuide extends Model
{
    use HasFactory;
    protected $table = 'account_deletion_guide';
    protected $fillable = ['ja_content', 'en_content'];
}
