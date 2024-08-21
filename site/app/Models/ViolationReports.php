<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationReports extends Model
{
    use HasFactory;

    protected $table = 'violation_reports';

    protected $fillable = [
        'message',
        'created_by',
        'user_id',
        'chat_room_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chatroom()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'chat_room_id');
    }
}
