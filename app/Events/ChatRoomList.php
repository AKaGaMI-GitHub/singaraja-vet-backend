<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatRoomList
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $lastMessage;

    /**
     * Create a new event instance.
     */
    public function __construct($roomId, $lastMessage)
    {
        //
        $this->roomId = $roomId;
        $this->lastMessage = $lastMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel LIST room chat user tertentu
        return [
            new Channel('chat-rooms'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'room.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->roomId,
            'last_message' => [
                'id' => $this->lastMessage->id,
                'message' => $this->lastMessage->message,
                'user_id' => $this->lastMessage->user_id,
                'user' => [
                    'id' => $this->lastMessage->user->id,
                    'name' => $this->lastMessage->user->name,
                    'avatar' => $this->lastMessage->user->avatar,
                ],
                'created_at' => $this->lastMessage->created_at->toDateTimeString(),
            ],
            'updated_at' => now()->toDateTimeString(),
        ];
    }
}
