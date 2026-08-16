<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ShopService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly ShopService $shopService
    ) {}

    public function index(Request $request): View
    {
        $latitude  = $request->get('lat');
        $longitude = $request->get('lng');
        $radius    = $request->get('radius', 5);
        $categories = Category::all();

        if ($latitude && $longitude) {
            $shops = $this->shopService->findNearbyShops((float) $latitude, (float) $longitude, (float) $radius);
        } else {
            $shops = $this->shopService->getAllActiveShops();
        }

        return view('buyer.home', compact('shops', 'categories', 'latitude', 'longitude'));
    }
}
