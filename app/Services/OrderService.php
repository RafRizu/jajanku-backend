<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderService
{
    /**
     * Add item to session-based cart.
     */
    public function addToCart(int $productId, int $quantity = 1): array
    {
        $product = Product::findOrFail($productId);
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->price,
                'image'      => $product->image,
                'quantity'   => $quantity,
                'shop_id'    => $product->shop_id,
            ];
        }

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
     * Update item quantity in cart.
     */
    public function updateCartItem(int $productId, int $quantity): array
    {
        $cart = Session::get('cart', []);
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
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
     * Create an order from the current cart.
     */
    public function checkout(int $buyerId, array $checkoutData): Order
    {
        return DB::transaction(function () use ($buyerId, $checkoutData) {
            $cart = Session::get('cart', []);

            if (empty($cart)) {
                throw new \Exception('Cart is empty.');
            }

            // Determine which shop — all items must be from same shop
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
                'notes'            => $checkoutData['notes'] ?? null,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            // Create a pending payment record
            Payment::create([
                'order_id' => $order->id,
                'method'   => $checkoutData['payment_method'] ?? 'transfer',
                'amount'   => $total + ($checkoutData['delivery_fee'] ?? 0),
                'status'   => 'pending',
            ]);

            $this->clearCart();

            return $order->load('items.product', 'payment', 'shop');
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
     */
    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order->fresh();
    }
}
