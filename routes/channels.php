<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels — Jajanku
|--------------------------------------------------------------------------
|
| Channel authorization menentukan siapa yang boleh subscribe ke channel apa.
|
*/

// ─── Private Channel: user.{userId} ──────────────────────────────────────────
// Hanya user yang bersangkutan yang bisa subscribe (buyer melihat status pesanannya)
Broadcast::channel('user.{userId}', function ($user, int $userId) {
    return (int) $user->id === $userId;
});

// ─── Private Channel: shop.{shopId} ──────────────────────────────────────────
// Hanya seller yang memiliki toko tersebut yang bisa subscribe
Broadcast::channel('shop.{shopId}', function ($user, int $shopId) {
    return $user->hasRole('seller') && $user->shop?->id === $shopId;
});

// ─── Public Channel: driver.jobs ──────────────────────────────────────────────
// Semua driver yang authenticated bisa subscribe
Broadcast::channel('driver.jobs', function ($user) {
    return $user->hasRole('driver');
});
