<?php

namespace App\Services;

use App\Events\NewDriverJobAvailable;
use App\Events\NewOrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderService
{
    /**
     * Add item to session-based cart with dynamic stock validation.
     */
    public function addToCart(int $productId, int $quantity = 1): array
    {
        $product = Product::findOrFail($productId);

        if (!$product->is_available || $product->stock <= 0) {
            throw new \Exception("Maaf, stok {$product->name} sedang habis.");
        }

        $cart = Session::get('cart', []);
        $currentQty = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->stock) {
            throw new \Exception("Stok {$product->name} tidak mencukupi. Sisa stok: {$product->stock}");
        }

        $cart[$productId] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => $product->price,
            'image'      => $product->image,
            'quantity'   => $newQty,
            'shop_id'    => $product->shop_id,
        ];

        Session::put('cart', $cart);
        return $cart;
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(int $productId): array
    {
        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::put('cart', $cart);
        return $cart;
    }

    /**
     * Update item quantity in cart with stock validation.
     */
    public function updateCartItem(int $productId, int $quantity): array
    {
        $cart = Session::get('cart', []);
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $product = Product::findOrFail($productId);
            if ($quantity > $product->stock) {
                throw new \Exception("Stok {$product->name} tidak mencukupi. Sisa stok: {$product->stock}");
            }
            $cart[$productId]['quantity'] = $quantity;
        }
        Session::put('cart', $cart);
        return $cart;
    }

    /**
     * Get cart with calculated totals.
     */
    public function getCart(): array
    {
        $cart  = Session::get('cart', []);
        $total = array_reduce($cart, fn ($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);

        return [
            'items' => $cart,
            'total' => $total,
            'count' => count($cart),
        ];
    }

    /**
     * Clear the cart.
     */
    public function clearCart(): void
    {
        Session::forget('cart');
    }

    /**
     * Create an order from the current cart and deduct product stock dynamically.
     */
    public function checkout(int $buyerId, array $checkoutData): Order
    {
        return DB::transaction(function () use ($buyerId, $checkoutData) {
            $cart = Session::get('cart', []);

            if (empty($cart)) {
                throw new \Exception('Keranjang Anda kosong.');
            }

            // Lock and validate stock for all products in cart
            foreach ($cart as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();
                if (!$product || !$product->is_available || $product->stock < $item['quantity']) {
                    $available = $product ? $product->stock : 0;
                    throw new \Exception("Stok untuk {$item['name']} tidak mencukupi (Sisa stok: {$available}). Silakan sesuaikan keranjang Anda.");
                }
            }

            $shopId = collect($cart)->first()['shop_id'];
            $total  = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'user_id'          => $buyerId,
                'shop_id'          => $shopId,
                'delivery_type'    => $checkoutData['delivery_type'] ?? 'delivery',
                'status'           => Order::STATUS_PENDING,
                'total_price'      => $total,
                'delivery_fee'     => $checkoutData['delivery_fee'] ?? 0,
                'delivery_address' => $checkoutData['delivery_address'] ?? null,
                'latitude'         => $checkoutData['latitude'] ?? null,
                'longitude'        => $checkoutData['longitude'] ?? null,
                'notes'            => $checkoutData['notes'] ?? null,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);

                // Deduct stock dynamically & update availability
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();
                if ($product) {
                    $newStock = max(0, $product->stock - $item['quantity']);
                    $product->update([
                        'stock'        => $newStock,
                        'is_available' => $newStock > 0,
                    ]);
                }
            }

            // Create a pending payment record
            Payment::create([
                'order_id' => $order->id,
                'method'   => $checkoutData['payment_method'] ?? 'transfer',
                'amount'   => $total + ($checkoutData['delivery_fee'] ?? 0),
                'status'   => 'pending',
            ]);

            $this->clearCart();

            $order->load('items.product', 'payment', 'shop');

            // Broadcast ke seller: ada pesanan baru masuk
            broadcast(new NewOrderPlaced($order))->toOthers();

            return $order;
        });
    }

    /**
     * Upload payment proof image.
     */
    public function uploadPaymentProof(int $orderId, $file): Payment
    {
        $payment = Payment::where('order_id', $orderId)->firstOrFail();
        $path    = $file->store('payment-proofs', 'public');

        $payment->update([
            'proof_image' => $path,
            'status'      => 'paid',
            'paid_at'     => now(),
        ]);

        // Update order status to confirmed
        $payment->order->update(['status' => Order::STATUS_CONFIRMED]);

        return $payment;
    }

    /**
     * Update order status (for sellers and drivers).
     * Restores stock atomically if order is cancelled.
     */
    public function updateStatus(Order $order, string $status): Order
    {
        return DB::transaction(function () use ($order, $status) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$lockedOrder) {
                return $order;
            }

            $previousStatus = $lockedOrder->status;
            $lockedOrder->update(['status' => $status]);
            $updatedOrder = $lockedOrder->fresh();

            // Restore stock if cancelled
            if ($status === Order::STATUS_CANCELLED && $previousStatus !== Order::STATUS_CANCELLED) {
                foreach ($updatedOrder->items as $item) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                        $product->update(['is_available' => true]);
                    }
                }
            }

            // Broadcast ke buyer & seller: status berubah
            broadcast(new OrderStatusUpdated($updatedOrder));

            // Jika status menjadi on_delivery DAN tipe pengiriman adalah delivery (bukan pickup), beritahu driver
            if ($status === Order::STATUS_ON_DELIVERY && $updatedOrder->delivery_type === 'delivery') {
                broadcast(new NewDriverJobAvailable($updatedOrder));
            }

            return $updatedOrder;
        });
    }
}
