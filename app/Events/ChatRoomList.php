<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatRoomList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct() {}

    public function broadcastOn(): array
    {
        return [ 
            new Channel('chat-rooms') 
        ];
    }

    public function broadcastAs(): string
    {
        return 'room.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'Daftar room chat diperbarui',
            'triggered_at' => now()->toISOString(),
        ];
    }
}
