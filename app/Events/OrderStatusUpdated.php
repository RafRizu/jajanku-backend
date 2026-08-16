<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Order $order
    ) {}

    /**
     * Get the channels the event should broadcast on.
     * Buyer dan seller masing-masing mendapat notifikasi di channel private mereka.
     */
    public function broadcastOn(): array
    {
        return [
            // Notifikasi ke buyer
            new PrivateChannel("user.{$this->order->user_id}"),
            // Notifikasi ke seller (toko)
            new PrivateChannel("shop.{$this->order->shop_id}"),
        ];
    }

    /**
     * Data yang dikirim ke client.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id'   => $this->order->id,
            'status'     => $this->order->status,
            'status_label' => $this->order->status_label,
            'updated_at' => $this->order->updated_at->toIso8601String(),
        ];
    }

    /**
     * Event name yang dikirim ke Pusher.
     */
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }
}
