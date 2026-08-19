<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Get buyer's orders.
     * GET /api/orders
     */
    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['shop', 'items.product', 'payment'])
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Get a specific order.
     * GET /api/orders/{id}
     */
    public function show(int $orderId): JsonResponse
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['shop', 'items.product', 'payment', 'driver'])
            ->findOrFail($orderId);

        return response()->json(['success' => true, 'data' => $order]);
    }

    /**
     * Create an order from cart.
     * POST /api/orders
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shop_id'          => ['required', 'exists:shops,id'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'delivery_type'    => ['required', 'in:delivery,pickup'],
            'delivery_address' => ['required_if:delivery_type,delivery', 'string'],
            'payment_method'   => ['required', 'in:transfer,cash,qris,midtrans'],
            'notes'            => ['nullable', 'string'],
        ]);

        // Build cart from request items
        foreach ($request->items as $item) {
            $this->orderService->addToCart($item['product_id'], $item['quantity']);
        }

        $order = $this->orderService->checkout(Auth::id(), $request->only([
            'delivery_type',
            'delivery_address',
            'payment_method',
            'notes',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat.',
            'data'    => $order,
        ], 201);
    }

    /**
     * Upload payment proof.
     * POST /api/orders/{id}/payment-proof
     */
    public function uploadProof(Request $request, int $orderId): JsonResponse
    {
        $request->validate([
            'proof_image' => ['required', 'image', 'max:2048'],
        ]);

        $order   = Order::where('user_id', Auth::id())->findOrFail($orderId);
        $payment = $this->orderService->uploadPaymentProof($orderId, $request->file('proof_image'));

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah.',
            'data'    => $payment,
        ]);
    }

    /**
     * Seller: Get shop orders.
     * GET /api/seller/orders
     */
    public function sellerOrders(): JsonResponse
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            return response()->json(['success' => false, 'message' => 'Warung tidak ditemukan.'], 404);
        }

        $orders = Order::where('shop_id', $shop->id)
            ->with(['buyer', 'items.product', 'payment'])
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Seller: Update order status.
     * PATCH /api/seller/orders/{id}/status
     */
    public function updateStatus(Request $request, int $orderId): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:confirmed,processing,on_delivery,cancelled'],
        ]);

        $shop  = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->findOrFail($orderId);

        $this->orderService->updateStatus($order, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.',
            'data'    => $order->fresh(),
        ]);
    }

    /**
     * Driver: Get available jobs.
     * GET /api/driver/jobs
     */
    public function driverJobs(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => ['nullable', 'numeric'],
            'lng'    => ['nullable', 'numeric'],
        ]);

        $orders = Order::where('status', 'on_delivery')
            ->where('delivery_type', 'delivery')
            ->whereNull('driver_id')
            ->with(['shop', 'buyer'])
            ->latest()
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Driver: Accept and complete delivery.
     * PATCH /api/driver/orders/{id}/status
     */
    public function driverUpdateStatus(Request $request, int $orderId): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:on_delivery,delivered'],
        ]);

        $order = Order::findOrFail($orderId);

        if ($request->status === 'on_delivery' && is_null($order->driver_id)) {
            $order->update(['driver_id' => Auth::id()]);
        }

        if ($order->driver_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $this->orderService->updateStatus($order, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status pengiriman berhasil diperbarui.',
            'data'    => $order->fresh(),
        ]);
    }
}
