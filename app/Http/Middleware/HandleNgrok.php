<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fix ngrok interstitial page issue.
 *
 * Ngrok v3 menampilkan halaman "browser warning" untuk request yang
 * tidak punya header 'ngrok-skip-browser-warning'.
 * Middleware ini menambahkan response header agar bypass itu tidak diperlukan,
 * sekaligus memastikan HTTPS scheme terbaca dengan benar saat behind ngrok.
 */
class HandleNgrok
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan Laravel membaca HTTPS scheme dari X-Forwarded-Proto ngrok
        if ($request->header('X-Forwarded-Proto') === 'https') {
            $request->server->set('HTTPS', 'on');
        }

        /** @var Response $response */
        $response = $next($request);

        // Tambahkan header agar client tahu ini bukan halaman ngrok warning
        $response->headers->set('ngrok-skip-browser-warning', 'true');

        return $response;
    }
}
