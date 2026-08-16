<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Jajanku Food Delivery
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically.
| Authentication via Laravel Sanctum bearer tokens.
|
*/

// ─── Public Routes ──────────────────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

Route::get('/shops',     [ShopController::class, 'index']);
Route::get('/shops/{id}', [ShopController::class, 'show']);

// ─── Authenticated Routes ────────────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/auth/me',      [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // ── Buyer ──────────────────────────────────────────────────────────────
    Route::middleware('role:buyer')->prefix('buyer')->group(function () {
        Route::get('/orders',                              [OrderController::class, 'index']);
        Route::get('/orders/{id}',                         [OrderController::class, 'show']);
        Route::post('/orders',                             [OrderController::class, 'store']);
        Route::post('/orders/{id}/payment-proof',          [OrderController::class, 'uploadProof']);
    });

    // ── Seller ──────────────────────────────────────────────────────────────
    Route::middleware('role:seller')->prefix('seller')->group(function () {
        Route::get('/orders',                              [OrderController::class, 'sellerOrders']);
        Route::patch('/orders/{id}/status',               [OrderController::class, 'updateStatus']);

        // Products (seller manages their own products)
        Route::apiResource('/products', \App\Http\Controllers\Api\ProductApiController::class)
            ->except(['index', 'show']);
        Route::get('/products', [\App\Http\Controllers\Api\ProductApiController::class, 'index']);
        Route::patch('/products/{id}/toggle', [\App\Http\Controllers\Api\ProductApiController::class, 'toggle']);

        // Shop profile
        Route::get('/shop',    [\App\Http\Controllers\Api\SellerShopController::class, 'show']);
        Route::post('/shop',   [\App\Http\Controllers\Api\SellerShopController::class, 'upsert']);
    });

    // ── Driver ──────────────────────────────────────────────────────────────
    Route::middleware('role:driver')->prefix('driver')->group(function () {
        Route::get('/jobs',                               [OrderController::class, 'driverJobs']);
        Route::patch('/orders/{id}/status',               [OrderController::class, 'driverUpdateStatus']);
    });

    // ── Admin ────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('/users',      \App\Http\Controllers\Api\Admin\UserController::class);
        Route::apiResource('/categories', \App\Http\Controllers\Api\Admin\CategoryController::class);
        Route::get('/shops',              [\App\Http\Controllers\Api\Admin\ShopAdminController::class, 'index']);
        Route::patch('/shops/{id}/status', [\App\Http\Controllers\Api\Admin\ShopAdminController::class, 'updateStatus']);
        Route::get('/orders',             [\App\Http\Controllers\Api\Admin\OrderAdminController::class, 'index']);
        Route::get('/stats',              [\App\Http\Controllers\Api\Admin\StatsController::class, 'index']);
    });
});
