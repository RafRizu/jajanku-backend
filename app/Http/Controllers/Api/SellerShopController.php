<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerShopController extends Controller
{
    public function __construct(
        private readonly ShopService $shopService
    ) {}

    /**
     * GET /api/seller/shop
     */
    public function show(): JsonResponse
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            return response()->json(['success' => false, 'message' => 'Warung belum dibuat.'], 404);
        }

        return response()->json(['success' => true, 'data' => $shop->load('products')]);
    }

    /**
     * POST /api/seller/shop
     */
    public function upsert(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address'     => ['required', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
        ]);

        $data = $request->only(['name', 'description', 'address', 'latitude', 'longitude']);
        $data['status'] = 'active';

        $shop = $this->shopService->upsertShop($data, Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Profil warung berhasil disimpan.',
            'data'    => $shop,
        ]);
    }
}
