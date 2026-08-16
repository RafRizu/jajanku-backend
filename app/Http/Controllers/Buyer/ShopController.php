<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ProductService;
use App\Services\ShopService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService   $shopService,
        private readonly ProductService $productService
    ) {}

    public function show(int $shopId, Request $request): View
    {
        $shop = $this->shopService->getShopWithMenu($shopId);
        $categories = Category::all();
        $activeCategory = $request->get('category');

        $products = $this->productService->getShopProducts(
            $shopId,
            $activeCategory ? (int) $activeCategory : null
        );

        return view('buyer.shop', compact('shop', 'categories', 'products', 'activeCategory'));
    }
}
