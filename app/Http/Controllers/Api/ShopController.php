<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService $shopService
    ) {}

    /**
     * Get nearby shops.
     * GET /api/shops?lat=&lng=&radius=5
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => ['nullable', 'numeric'],
            'lng'    => ['nullable', 'numeric'],
            'radius' => ['nullable', 'numeric', 'min:0.1', 'max:50'],
        ]);

        if ($request->filled('lat') && $request->filled('lng')) {
            $shops = $this->shopService->findNearbyShops(
                (float) $request->lat,
                (float) $request->lng,
                (float) $request->get('radius', 5)
            );
        } else {
            $shops = $this->shopService->getAllActiveShops();
        }

        return response()->json([
            'success' => true,
            'data'    => $shops,
        ]);
    }

    /**
     * Get shop detail with menu.
     * GET /api/shops/{id}
     */
    public function show(int $id): JsonResponse
    {
        $shop = $this->shopService->getShopWithMenu($id);

        return response()->json([
            'success' => true,
            'data'    => $shop,
        ]);
    }
}
