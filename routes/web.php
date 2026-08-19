<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\HomeController;
use App\Http\Controllers\Buyer\ShopController as BuyerShopController;
use App\Http\Controllers\Driver\DeliveryController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboard;
use App\Http\Controllers\Seller\ProductController;
use Illuminate\Support\Facades\Route;

/*
|─────────────────────────────────────────────────────────────────────────────
| Web Routes — Jajanku Food Delivery
|─────────────────────────────────────────────────────────────────────────────
*/

// ─── Public Routes ───────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login',     [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login',    [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ─── Authenticated Dashboard redirect ────────────────────────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('seller')) return redirect()->route('seller.dashboard');
    if ($user->hasRole('driver')) return redirect()->route('driver.jobs');
    if ($user->hasRole('admin'))  return redirect()->route('admin.dashboard');
    return redirect()->route('buyer.home');
})->middleware('auth')->name('dashboard');

// ─── Buyer Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/',                           [HomeController::class, 'index'])->name('home');
    Route::get('/shop/{id}',                  [BuyerShopController::class, 'show'])->name('shop');
    Route::get('/cart',                       [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add',                  [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update',               [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{productId}',        [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout',                   [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout',                  [CartController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/orders',                     [CartController::class, 'orderHistory'])->name('orders');
    Route::get('/orders/{id}',                [CartController::class, 'orderDetail'])->name('order.detail');
    Route::post('/orders/{id}/proof',         [CartController::class, 'uploadProof'])->name('order.proof');
});

// ─── Seller Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard',                  [SellerDashboard::class, 'index'])->name('dashboard');
    Route::get('/shop/edit',                  [SellerDashboard::class, 'editShop'])->name('shop.edit');
    Route::post('/shop',                      [SellerDashboard::class, 'updateShop'])->name('shop.update');
    Route::get('/orders',                     [SellerDashboard::class, 'orders'])->name('orders');
    Route::post('/orders/{id}/process',       [SellerDashboard::class, 'processOrder'])->name('orders.process');
    Route::post('/orders/{id}/cancel',        [SellerDashboard::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{id}/request-driver', [SellerDashboard::class, 'requestDriver'])->name('orders.driver');

    // Products
    Route::get('/products',                   [ProductController::class, 'index'])->name('products');
    Route::get('/products/create',            [ProductController::class, 'create'])->name('products.create');
    Route::post('/products',                  [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit',    [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}',         [ProductController::class, 'update'])->name('products.update');
    Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
    Route::delete('/products/{product}',      [ProductController::class, 'destroy'])->name('products.destroy');
});

// ─── Driver Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/jobs',                       [DeliveryController::class, 'jobs'])->name('jobs');
    Route::post('/jobs/{id}/accept',          [DeliveryController::class, 'accept'])->name('jobs.accept');
    Route::get('/delivery/{id}',              [DeliveryController::class, 'delivery'])->name('delivery');
    Route::post('/delivery/{id}/pickup',      [DeliveryController::class, 'confirmPickup'])->name('delivery.pickup');
    Route::post('/delivery/{id}/delivered',   [DeliveryController::class, 'confirmDelivered'])->name('delivery.delivered');
    Route::get('/history',                    [DeliveryController::class, 'history'])->name('history');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});
