<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Get products for a shop, optionally filtered by category.
     */
    public function getShopProducts(int $shopId, ?int $categoryId = null, int $perPage = 12): LengthAwarePaginator
    {
        return Product::where('shop_id', $shopId)
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->with('category')
            ->orderBy('is_available', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new product.
     */
    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }

        return Product::create($data);
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $image->store('products', 'public');
        }

        $product->update($data);
        return $product->fresh();
    }

    /**
     * Toggle product availability.
     */
    public function toggleAvailability(Product $product): Product
    {
        $product->update(['is_available' => !$product->is_available]);
        return $product->fresh();
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
    }
}
