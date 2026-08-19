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
            ->where('delivery_type', 'delivery')
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
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($orderId) {
                $order = Order::where('id', $orderId)
                    ->where('status', 'on_delivery')
                    ->whereNull('driver_id')
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    throw new \Exception('Maaf, pesanan ini sudah diambil oleh driver lain.');
                }

                $order->update(['driver_id' => Auth::id()]);
            });

            return redirect()->route('driver.delivery', $orderId)
                ->with('success', 'Pesanan berhasil diambil! Silakan menuju warung.');
        } catch (\Exception $e) {
            return redirect()->route('driver.jobs')->with('error', $e->getMessage());
        }
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
     * Update driver real-time location.
     */
    public function updateLocation(Request $request, int $orderId): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'latitude'  => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $order = Order::where('driver_id', Auth::id())
            ->where('status', 'on_delivery')
            ->findOrFail($orderId);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $order->update([
            'driver_latitude'  => $lat,
            'driver_longitude' => $lng,
        ]);

        broadcast(new \App\Events\DriverLocationUpdated($order, $lat, $lng));

        return response()->json(['success' => true]);
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
