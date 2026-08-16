<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDriverJobAvailable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Order $order
    ) {}

    /**
     * Broadcast ke channel publik driver — semua driver bisa mendengarkan.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('driver.jobs'),
        ];
    }

    public function broadcastWith(): array
    {
        $order = $this->order->load('shop', 'buyer');

        return [
            'order_id'         => $order->id,
            'shop_name'        => $order->shop->name ?? '-',
            'shop_address'     => $order->shop->address ?? '-',
            'delivery_address' => $order->delivery_address,
            'buyer_name'       => $order->buyer->name ?? '-',
            'total_price'      => $order->total_price,
            'created_at'       => $order->created_at->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'driver.job.new';
    }
}
