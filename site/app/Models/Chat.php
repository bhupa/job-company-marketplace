<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chat';
    protected $fillable = [
        'file',
        'seen',
        'message',
        'send_by',
        'chat_room_id',
        'admin_id',
        'payment_id',
        'created_at',
    ];

    public function sendBy()
    {
        return $this->belongsTo(User::class, 'send_by');
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'model_id')->where(
            'model',
            self::class
        );
    }

    public function violation()
    {
        return $this->belongsTo(ViolationReports::class, 'chat_room_id');
    }
}
