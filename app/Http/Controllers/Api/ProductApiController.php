<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        $shop = Auth::user()->shop;
        if (!$shop) {
            return response()->json(['success' => false, 'message' => 'Warung tidak ditemukan.'], 404);
        }

        $products = $this->productService->getShopProducts($shop->id, null, 50);

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'stock'       => ['integer', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

        $shop = Auth::user()->shop;
        if (!$shop) {
            return response()->json(['success' => false, 'message' => 'Warung tidak ditemukan.'], 404);
        }

        $data = array_merge($request->only(['name', 'price', 'category_id', 'description', 'stock']), [
            'shop_id'      => $shop->id,
            'is_available' => true,
        ]);

        $product = $this->productService->createProduct($data, $request->file('image'));

        return response()->json(['success' => true, 'data' => $product], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $shop    = Auth::user()->shop;
        $product = Product::where('shop_id', $shop?->id)->findOrFail($id);

        $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'price'       => ['sometimes', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'stock'       => ['integer', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

        $product = $this->productService->updateProduct($product, $request->only(['name', 'price', 'category_id', 'description', 'stock']), $request->file('image'));

        return response()->json(['success' => true, 'data' => $product]);
    }

    public function destroy(int $id): JsonResponse
    {
        $shop    = Auth::user()->shop;
        $product = Product::where('shop_id', $shop?->id)->findOrFail($id);
        $this->productService->deleteProduct($product);

        return response()->json(['success' => true, 'message' => 'Produk dihapus.']);
    }

    public function toggle(int $id): JsonResponse
    {
        $shop    = Auth::user()->shop;
        $product = Product::where('shop_id', $shop?->id)->findOrFail($id);
        $product = $this->productService->toggleAvailability($product);

        return response()->json(['success' => true, 'data' => $product]);
    }
}
