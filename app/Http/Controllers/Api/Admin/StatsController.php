<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'total_users'   => User::count(),
                'total_shops'   => Shop::count(),
                'total_orders'  => Order::count(),
                'total_revenue' => Order::where('status', 'delivered')->sum('total_price'),
                'pending_orders' => Order::where('status', 'pending')->count(),
            ],
        ]);
    }
}
