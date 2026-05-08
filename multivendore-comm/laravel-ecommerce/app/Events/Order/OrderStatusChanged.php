<?php

namespace App\Events\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Order       $order,
        public readonly OrderStatus $previous,
        public readonly OrderStatus $current,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("user.{$this->order->user_id}");
    }

    public function broadcastAs(): string
    {
        return 'order.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'previous'     => $this->previous->value,
            'current'      => $this->current->value,
        ];
    }
}
