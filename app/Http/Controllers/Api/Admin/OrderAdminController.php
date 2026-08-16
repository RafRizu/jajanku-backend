<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with(['buyer', 'shop', 'driver', 'payment'])->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $orders]);
    }
}
