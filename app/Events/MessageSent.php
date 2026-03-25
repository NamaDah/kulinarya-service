<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageData;
    public $orderId;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->orderId = $message->order_id;
        $this->messageData = [
            'id' => $message->id,
            'order_id' => $message->order_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name ?? '',
            'receiver_id' => $message->receiver_id,
            'message' => $message->message,
            'created_at' => $message->created_at->toISOString(),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order-chat.' . $this->orderId),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
