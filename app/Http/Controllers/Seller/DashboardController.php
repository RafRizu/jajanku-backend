<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\ShopService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ShopService  $shopService,
        private readonly OrderService $orderService
    ) {}

    public function index(): View
    {
        $shop   = Auth::user()->shop;
        $orders = $shop
            ? Order::where('shop_id', $shop->id)->with(['buyer', 'items', 'payment'])->latest()->limit(5)->get()
            : collect();

        $stats = $shop ? [
            'total_orders'   => Order::where('shop_id', $shop->id)->count(),
            'pending_orders' => Order::where('shop_id', $shop->id)->where('status', 'pending')->count(),
            'total_products' => $shop->products()->count(),
            'revenue'        => Order::where('shop_id', $shop->id)->where('status', 'delivered')->sum('total_price'),
        ] : [];

        return view('seller.dashboard', compact('shop', 'orders', 'stats'));
    }

    public function editShop(): View
    {
        $shop = Auth::user()->shop;
        return view('seller.shop-edit', compact('shop'));
    }

    public function updateShop(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address'     => ['required', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data = $request->only(['name', 'description', 'address', 'latitude', 'longitude']);

        if ($request->hasFile('image')) {
            $shop = Auth::user()->shop;
            if ($shop && $shop->image) {
                Storage::disk('public')->delete($shop->image);
            }
            $data['image'] = $request->file('image')->store('shops', 'public');
        }

        $data['status'] = 'active';
        $this->shopService->upsertShop($data, Auth::id());

        return redirect()->route('seller.dashboard')->with('success', 'Profil warung berhasil diperbarui!');
    }

    public function orders(): View
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            return redirect()->route('seller.shop.edit')->with('error', 'Silakan lengkapi profil warung Anda terlebih dahulu.');
        }

        $orders = Order::where('shop_id', $shop->id)
            ->with(['buyer', 'items.product', 'payment', 'driver'])
            ->latest()
            ->paginate(15);

        return view('seller.orders', compact('orders', 'shop'));
    }

    public function processOrder(Request $request, int $orderId): RedirectResponse
    {
        $shop  = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->findOrFail($orderId);

        $newStatus = match ($order->status) {
            'pending'     => 'confirmed',
            'confirmed'   => 'processing',
            'processing'  => 'on_delivery',
            'on_delivery' => $order->delivery_type === 'pickup' ? 'delivered' : $order->status,
            default       => $order->status,
        };

        $this->orderService->updateStatus($order, $newStatus);

        $msg = ($order->delivery_type === 'pickup' && $newStatus === 'delivered')
            ? 'Pesanan telah diselesaikan (sudah diambil oleh pembeli).'
            : 'Status pesanan berhasil diperbarui.';

        return back()->with('success', $msg);
    }

    public function cancelOrder(Request $request, int $orderId): RedirectResponse
    {
        $shop  = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->findOrFail($orderId);

        if (in_array($order->status, ['processing', 'on_delivery', 'delivered'])) {
            return back()->with('error', 'Pesanan yang sedang dimasak atau diantar/diambil tidak dapat dibatalkan.');
        }

        $this->orderService->updateStatus($order, 'cancelled');

        if ($order->payment) {
            $order->payment->update(['status' => 'failed']);
        }

        return back()->with('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
    }

    public function requestDriver(int $orderId): RedirectResponse
    {
        $shop  = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->findOrFail($orderId);

        $this->orderService->updateStatus($order, 'on_delivery');

        $message = $order->delivery_type === 'pickup'
            ? 'Pesanan telah ditandai Siap Diambil di Warung.'
            : 'Driver telah diminta. Pesanan menunggu driver.';

        return back()->with('success', $message);
    }
}
