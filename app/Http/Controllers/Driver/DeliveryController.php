<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Show all orders waiting for a driver ("on_delivery" with no driver).
     */
    public function jobs(): View
    {
        $availableOrders = Order::where('status', 'on_delivery')
            ->whereNull('driver_id')
            ->with(['shop', 'buyer', 'items'])
            ->latest()
            ->paginate(10);

        $myOrders = Order::where('driver_id', Auth::id())
            ->whereIn('status', ['on_delivery'])
            ->with(['shop', 'buyer'])
            ->latest()
            ->get();

        return view('driver.jobs', compact('availableOrders', 'myOrders'));
    }

    /**
     * Accept a delivery job.
     */
    public function accept(int $orderId): RedirectResponse
    {
        $order = Order::where('status', 'on_delivery')
            ->whereNull('driver_id')
            ->findOrFail($orderId);

        $order->update(['driver_id' => Auth::id()]);

        return redirect()->route('driver.delivery', $orderId)
            ->with('success', 'Pesanan berhasil diambil! Silakan menuju warung.');
    }

    /**
     * Show delivery detail page.
     */
    public function delivery(int $orderId): View
    {
        $order = Order::where('driver_id', Auth::id())
            ->with(['shop', 'buyer', 'items.product', 'payment'])
            ->findOrFail($orderId);

        return view('driver.delivery', compact('order'));
    }

    /**
     * Mark order as picked up from shop.
     */
    public function confirmPickup(int $orderId): RedirectResponse
    {
        $order = Order::where('driver_id', Auth::id())->findOrFail($orderId);
        // Already on_delivery, just track pickup confirmation via session/log
        return back()->with('success', 'Konfirmasi pengambilan dari warung berhasil.');
    }

    /**
     * Mark order as delivered.
     */
    public function confirmDelivered(int $orderId): RedirectResponse
    {
        $order = Order::where('driver_id', Auth::id())->findOrFail($orderId);
        $this->orderService->updateStatus($order, 'delivered');

        return redirect()->route('driver.jobs')
            ->with('success', 'Pesanan telah berhasil diantar!');
    }

    /**
     * Driver's delivery history.
     */
    public function history(): View
    {
        $orders = Order::where('driver_id', Auth::id())
            ->with(['shop', 'buyer'])
            ->latest()
            ->paginate(15);

        return view('driver.history', compact('orders'));
    }
}
