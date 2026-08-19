<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function index(): View
    {
        $cartData = $this->orderService->getCart();
        return view('buyer.cart', $cartData);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['integer', 'min:1'],
        ]);

        try {
            $cart = $this->orderService->addToCart(
                (int) $request->product_id,
                (int) $request->get('quantity', 1)
            );

            $cartData = $this->orderService->getCart();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang!',
                'count'   => $cartData['count'],
                'total'   => $cartData['total'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function remove(int $productId): RedirectResponse
    {
        $this->orderService->removeFromCart($productId);
        return back()->with('success', 'Item berhasil dihapus.');
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity'   => ['required', 'integer', 'min:0'],
        ]);

        try {
            $cart = $this->orderService->updateCartItem(
                (int) $request->product_id,
                (int) $request->quantity
            );

            $cartData = $this->orderService->getCart();

            return response()->json([
                'success' => true,
                'count'   => $cartData['count'],
                'total'   => $cartData['total'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function checkout(): View
    {
        $cartData = $this->orderService->getCart();

        if (empty($cartData['items'])) {
            return redirect()->route('buyer.home')->with('error', 'Keranjang Anda kosong.');
        }

        return view('buyer.checkout', $cartData);
    }

    public function processCheckout(Request $request): RedirectResponse
    {
        $request->validate([
            'delivery_type'    => ['required', 'in:delivery,pickup'],
            'delivery_address' => ['nullable', 'required_if:delivery_type,delivery', 'string', 'max:500'],
            'payment_method'   => ['required', 'in:transfer,cash,qris'],
            'notes'            => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $data = $request->only([
                'delivery_type',
                'delivery_address',
                'payment_method',
                'notes',
                'delivery_fee',
            ]);

            if ($data['delivery_type'] === 'pickup') {
                $data['delivery_address'] = 'Ambil Sendiri di Warung';
                $data['delivery_fee'] = 0;
            }

            $order = $this->orderService->checkout(Auth::id(), $data);

            return redirect()->route('buyer.order.detail', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function uploadProof(Request $request, int $orderId): RedirectResponse
    {
        $request->validate([
            'proof_image' => ['required', 'image', 'max:2048'],
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        $this->orderService->uploadPaymentProof($orderId, $request->file('proof_image'));

        return back()->with('success', 'Bukti pembayaran berhasil diunggah!');
    }

    public function orderDetail(int $orderId): View
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product', 'shop', 'payment', 'driver'])
            ->findOrFail($orderId);

        return view('buyer.order-detail', compact('order'));
    }

    public function orderHistory(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['shop', 'payment'])
            ->latest()
            ->paginate(10);

        return view('buyer.orders', compact('orders'));
    }
}
