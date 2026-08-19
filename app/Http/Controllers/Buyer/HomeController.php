<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        // Aplikasi ini menaungi 1 warung tunggal: ambil warung pertama yang aktif
        $shop = Shop::where('status', 'active')->first();

        $categories = Category::all();

        // Query produk dari warung tunggal
        $query = Product::where('is_available', true)
            ->with('category');

        if ($shop) {
            $query->where('shop_id', $shop->id);
        }

        // Filter Kategori
        if ($request->filled('cat')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->get('cat')));
        }

        // Search Query
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(66)->withQueryString();

        return view('buyer.home', compact('shop', 'products', 'categories'));
    }
}
