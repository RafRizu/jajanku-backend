<?php

namespace App\Services;

use App\Models\Shop;
use Illuminate\Pagination\LengthAwarePaginator;

class ShopService
{
    /**
     * Find nearby shops using Haversine formula.
     * Works with any SQL database (SQLite, MySQL, PostgreSQL).
     *
     * @param float $latitude  Buyer's latitude
     * @param float $longitude Buyer's longitude
     * @param float $radiusKm  Search radius in kilometers
     * @param int   $perPage
     * @return LengthAwarePaginator
     */
    public function findNearbyShops(
        float $latitude,
        float $longitude,
        float $radiusKm = 5,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Shop::nearby($latitude, $longitude, $radiusKm)
            ->with(['owner', 'activeProducts'])
            ->paginate($perPage);
    }

    /**
     * Get all active shops (fallback when no location provided).
     */
    public function getAllActiveShops(int $perPage = 10): LengthAwarePaginator
    {
        return Shop::active()
            ->with(['owner', 'activeProducts'])
            ->paginate($perPage);
    }

    /**
     * Get shop details with products grouped by category.
     */
    public function getShopWithMenu(int $shopId): Shop
    {
        return Shop::with([
            'owner',
            'products' => fn ($q) => $q->where('is_available', true)->with('category'),
        ])->findOrFail($shopId);
    }

    /**
     * Create or update a shop for a seller.
     */
    public function upsertShop(array $data, int $userId): Shop
    {
        return Shop::updateOrCreate(
            ['user_id' => $userId],
            array_merge($data, ['user_id' => $userId])
        );
    }
}
