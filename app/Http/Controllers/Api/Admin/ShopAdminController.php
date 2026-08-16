<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Shop::with('owner')->paginate(20)]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:active,inactive,pending']]);
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => $request->status]);
        return response()->json(['success' => true, 'data' => $shop]);
    }
}
