<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('send-message.{roomId}', function ($roomId) {
    return \App\Models\ChatMessage::with('user')->where('room_id', $roomId)->exists();
});
