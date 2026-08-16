<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Order $order
    ) {}

    /**
     * Broadcast ke channel seller (toko yang menerima order baru).
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("shop.{$this->order->shop_id}"),
        ];
    }

    public function broadcastWith(): array
    {
        $order = $this->order->load('buyer', 'items.product');

        return [
            'order_id'     => $order->id,
            'buyer_name'   => $order->buyer->name ?? '-',
            'total_price'  => $order->total_price,
            'items_count'  => $order->items->count(),
            'delivery_type' => $order->delivery_type,
            'status'       => $order->status,
            'status_label' => $order->status_label,
            'created_at'   => $order->created_at->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.new';
    }
}
