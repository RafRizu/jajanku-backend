<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $orderId;
    public int $driverId;
    public float $latitude;
    public float $longitude;
    public int $buyerId;

    public function __construct(Order $order, float $latitude, float $longitude)
    {
        $this->orderId   = $order->id;
        $this->driverId  = $order->driver_id ?? 0;
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
        $this->buyerId   = $order->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->buyerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'driver.location.updated';
    }
}
