<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $status;
    public $driverName;

    /**
     * Create a new event instance.
     */
    public function __construct(int $orderId, string $status, ?string $driverName = null)
    {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->driverName = $driverName;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->orderId),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }
}
