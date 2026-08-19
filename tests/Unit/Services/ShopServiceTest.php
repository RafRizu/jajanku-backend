<?php

namespace Tests\Unit\Services;

use App\Models\Shop;
use App\Models\User;
use App\Services\ShopService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShopService $shopService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopService = new ShopService();
    }

    public function test_can_upsert_shop_creation(): void
    {
        $user = User::factory()->create();

        $shop = $this->shopService->upsertShop([
            'name' => 'Kopi Kampus',
            'address' => 'Gedung A FT',
            'phone' => '089988776655',
            'is_open' => true,
        ], $user->id);

        $this->assertInstanceOf(Shop::class, $shop);
        $this->assertEquals('Kopi Kampus', $shop->name);
        $this->assertEquals($user->id, $shop->user_id);
    }

    public function test_can_upsert_shop_update(): void
    {
        $user = User::factory()->create();

        $initialShop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Kopi Lama',
            'address' => 'Kantin Lama',
            'phone' => '08123',
            'is_open' => true,
        ]);

        $updatedShop = $this->shopService->upsertShop([
            'name' => 'Kopi Baru',
            'address' => 'Kantin Baru',
            'status' => 'inactive',
        ], $user->id);

        $this->assertEquals($initialShop->id, $updatedShop->id);
        $this->assertEquals('Kopi Baru', $updatedShop->name);
        $this->assertEquals('inactive', $updatedShop->status);
        $this->assertDatabaseCount('shops', 1);
    }
}
