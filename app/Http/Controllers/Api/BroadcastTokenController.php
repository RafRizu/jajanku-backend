<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastTokenController extends Controller
{
    /**
     * Return Pusher channel auth token.
     * POST /broadcasting/auth  (already handled by Laravel)
     *
     * Endpoint ini membantu client (Flutter/React Native) mendapatkan
     * info channel yang bisa mereka subscribe berdasarkan role.
     *
     * GET /api/realtime/channels
     */
    public function channels(): JsonResponse
    {
        $user = Auth::user();
        $channels = [];

        // Channel yang bisa disubscribe user ini
        $channels[] = [
            'name'    => "private-user.{$user->id}",
            'purpose' => 'Notifikasi status pesanan kamu',
        ];

        if ($user->hasRole('seller') && $user->shop) {
            $channels[] = [
                'name'    => "private-shop.{$user->shop->id}",
                'purpose' => 'Notifikasi pesanan baru masuk ke toko kamu',
            ];
        }

        if ($user->hasRole('driver')) {
            $channels[] = [
                'name'    => 'driver.jobs',
                'purpose' => 'Notifikasi job pengiriman baru',
            ];
        }

        return response()->json([
            'success'  => true,
            'pusher'   => [
                'key'     => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            ],
            'channels' => $channels,
        ]);
    }
}
