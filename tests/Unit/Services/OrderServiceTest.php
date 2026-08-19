<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\ShopService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;
    private User $buyer;
    private Shop $shop;
    private Product $product1;
    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();

        $seller = User::factory()->create();
        $this->buyer = User::factory()->create();

        $this->shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'Warung Mas Joko',
            'address' => 'Jl. Kampus No. 1',
            'phone' => '08123456789',
            'is_open' => true,
        ]);

        $category = Category::create([
            'name' => 'Makanan Utama',
            'slug' => 'makanan-utama',
        ]);

        $this->product1 = Product::create([
            'shop_id' => $this->shop->id,
            'category_id' => $category->id,
            'name' => 'Nasi Goreng Spesial',
            'price' => 15000,
            'is_available' => true,
            'stock' => 50,
        ]);

        $this->product2 = Product::create([
            'shop_id' => $this->shop->id,
            'category_id' => $category->id,
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'is_available' => true,
            'stock' => 50,
        ]);
    }

    public function test_can_add_items_to_cart_in_session(): void
    {
        $cart = $this->orderService->addToCart($this->product1->id, 2);

        $this->assertCount(1, $cart);
        $this->assertEquals(2, $cart[$this->product1->id]['quantity']);
        $this->assertEquals(15000, $cart[$this->product1->id]['price']);
        $this->assertEquals(Session::get('cart'), $cart);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $this->orderService->addToCart($this->product1->id, 1);
        $cart = $this->orderService->updateCartItem($this->product1->id, 5);

        $this->assertEquals(5, $cart[$this->product1->id]['quantity']);
    }

    public function test_updating_cart_item_quantity_to_zero_removes_item(): void
    {
        $this->orderService->addToCart($this->product1->id, 1);
        $cart = $this->orderService->updateCartItem($this->product1->id, 0);

        $this->assertArrayNotHasKey($this->product1->id, $cart);
    }

    public function test_can_remove_item_from_cart(): void
    {
        $this->orderService->addToCart($this->product1->id, 1);
        $this->orderService->addToCart($this->product2->id, 2);

        $cart = $this->orderService->removeFromCart($this->product1->id);

        $this->assertCount(1, $cart);
        $this->assertArrayNotHasKey($this->product1->id, $cart);
        $this->assertArrayHasKey($this->product2->id, $cart);
    }

    public function test_can_get_cart_totals(): void
    {
        $this->orderService->addToCart($this->product1->id, 2); // 2 x 15.000 = 30.000
        $this->orderService->addToCart($this->product2->id, 1); // 1 x 5.000 = 5.000

        $cartData = $this->orderService->getCart();

        $this->assertEquals(35000, $cartData['total']);
        $this->assertEquals(2, $cartData['count']);
    }

    public function test_pickup_order_does_not_broadcast_driver_job(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\NewDriverJobAvailable::class,
            \App\Events\OrderStatusUpdated::class,
        ]);

        $order = Order::create([
            'user_id'       => $this->buyer->id,
            'shop_id'       => $this->shop->id,
            'delivery_type' => 'pickup',
            'status'        => Order::STATUS_PROCESSING,
            'total_price'   => 15000,
            'delivery_fee'  => 0,
        ]);

        $this->assertEquals('Sedang Diproses', $order->status_label);

        $updated = $this->orderService->updateStatus($order, Order::STATUS_ON_DELIVERY);

        $this->assertEquals('Siap Diambil', $updated->status_label);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\OrderStatusUpdated::class);
        \Illuminate\Support\Facades\Event::assertNotDispatched(\App\Events\NewDriverJobAvailable::class);
    }
}
