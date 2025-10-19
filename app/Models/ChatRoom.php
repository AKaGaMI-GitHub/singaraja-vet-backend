<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function getListRoomChat()
    {
        return self::with([
            'user',
            'lastMessage.user'
        ])
            ->withMax('message', 'created_at') // ambil waktu terakhir dari relasi message
            ->orderByDesc('message_max_created_at') // urutkan berdasarkan waktu terakhir pesan
            ->get()
            ->map(function ($room) {
                return [
                    'room_id' => $room->room_id,
                    'user' => $room->user,
                    'last_message' => $room->lastMessage ? [
                        'id' => $room->lastMessage->id,
                        'message' => $room->lastMessage->message,
                        'sender' => $room->lastMessage->user,
                        'created_at' => $room->lastMessage->created_at,
                    ] : null,
                    'created_at' => $room->created_at,
                ];
            });
    }

    public function message()
    {
        return $this->hasMany(ChatMessage::class, 'room_id', 'room_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'room_id', 'room_id')
            ->latestOfMany('created_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
