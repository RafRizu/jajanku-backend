<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(): View
    {
        $shop     = Auth::user()->shop;
        $products = $shop
            ? $this->productService->getShopProducts($shop->id, null, 20)
            : collect()->paginate(20);

        $categories = Category::all();

        return view('seller.products', compact('products', 'categories', 'shop'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('seller.product-form', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'stock'       => ['integer', 'min:0'],
        ]);

        $data            = $request->only(['name', 'description', 'price', 'category_id', 'stock']);
        $data['shop_id'] = Auth::user()->shop->id;
        $data['is_available'] = true;

        $this->productService->createProduct($data, $request->file('image'));

        return redirect()->route('seller.products')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product): View
    {
        $this->authorizeProduct($product);
        $categories = Category::all();
        return view('seller.product-form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($product);

        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'stock'       => ['integer', 'min:0'],
        ]);

        $data = $request->only(['name', 'description', 'price', 'category_id', 'stock']);
        $this->productService->updateProduct($product, $data, $request->file('image'));

        return redirect()->route('seller.products')->with('success', 'Produk berhasil diperbarui!');
    }

    public function toggleAvailability(Product $product): RedirectResponse
    {
        $this->authorizeProduct($product);
        $this->productService->toggleAvailability($product);

        $label = $product->is_available ? 'dinonaktifkan' : 'diaktifkan';
        return back()->with('success', "Produk berhasil {$label}.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorizeProduct($product);
        $this->productService->deleteProduct($product);
        return redirect()->route('seller.products')->with('success', 'Produk berhasil dihapus.');
    }

    private function authorizeProduct(Product $product): void
    {
        $shop = Auth::user()->shop;
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized.');
        }
    }
}
