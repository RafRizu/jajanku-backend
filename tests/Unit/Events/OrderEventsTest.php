<?php

namespace Tests\Unit\Events;

use App\Events\NewDriverJobAvailable;
use App\Events\NewOrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderEventsTest extends TestCase
{
    use RefreshDatabase;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::factory()->create(['name' => 'Pak Joko']);
        $buyer = User::factory()->create(['name' => 'Budi']);

        $shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'Warung Pak Joko',
            'address' => 'Jl. Pendidikan No. 5',
            'phone' => '0812345678',
            'is_open' => true,
        ]);

        $category = Category::create([
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => 'Bakso Solo',
            'price' => 15000,
            'is_available' => true,
        ]);

        $this->order = Order::create([
            'user_id' => $buyer->id,
            'shop_id' => $shop->id,
            'delivery_type' => 'delivery',
            'status' => 'pending',
            'total_price' => 15000,
            'delivery_fee' => 3000,
            'delivery_address' => 'Asrama Putra No. 12',
        ]);
    }

    public function test_order_status_updated_event_broadcast_data_and_channels(): void
    {
        $event = new OrderStatusUpdated($this->order);

        $channels = $event->broadcastOn();
        $this->assertCount(2, $channels);
        $this->assertEquals("private-user.{$this->order->user_id}", $channels[0]->name);
        $this->assertEquals("private-shop.{$this->order->shop_id}", $channels[1]->name);

        $this->assertEquals('order.status.updated', $event->broadcastAs());

        $payload = $event->broadcastWith();
        $this->assertEquals($this->order->id, $payload['order_id']);
        $this->assertEquals('pending', $payload['status']);
        $this->assertEquals('Menunggu Konfirmasi', $payload['status_label']);
    }

    public function test_new_order_placed_event_broadcast_data_and_channel(): void
    {
        $event = new NewOrderPlaced($this->order);

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals("private-shop.{$this->order->shop_id}", $channels[0]->name);
        $this->assertEquals('order.new', $event->broadcastAs());

        $payload = $event->broadcastWith();
        $this->assertEquals($this->order->id, $payload['order_id']);
        $this->assertEquals('Budi', $payload['buyer_name']);
        $this->assertEquals(15000, $payload['total_price']);
    }

    public function test_new_driver_job_available_event_broadcast_data_and_channel(): void
    {
        $event = new NewDriverJobAvailable($this->order);

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals('driver.jobs', $channels[0]->name);
        $this->assertEquals('driver.job.new', $event->broadcastAs());

        $payload = $event->broadcastWith();
        $this->assertEquals($this->order->id, $payload['order_id']);
        $this->assertEquals('Warung Pak Joko', $payload['shop_name']);
        $this->assertEquals('Asrama Putra No. 12', $payload['delivery_address']);
    }
}
