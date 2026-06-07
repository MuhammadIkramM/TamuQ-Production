<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {

        $nonce = base64_encode(random_bytes(16));

        app()->instance('csp-nonce', $nonce);
        View::share('cspNonce', $nonce);

        $response = $next($request);

        $response->headers->set('X-Security-Header-Status', 'Active');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');

        $cspDirectives = [
            "default-src 'self'",

            "script-src 'self' 'nonce-{$nonce}' 'wasm-unsafe-eval' blob: https://maps.googleapis.com https://maps.gstatic.com",

            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://maps.googleapis.com https://maps.gstatic.com",

            "img-src 'self' data: blob: https://maps.gstatic.com https://maps.googleapis.com https://maps.googleusercontent.com https://lh3.googleusercontent.com https://www.transparenttextures.com https://ui-avatars.com https://images.unsplash.com",

            "frame-src 'self' https://www.google.com https://maps.google.com",

            "connect-src 'self' blob: https://maps.googleapis.com https://tamuq.my.id",

            "font-src 'self' data: https://fonts.gstatic.com",

            "worker-src 'self' blob:",

            "frame-ancestors 'self'",

            "form-action 'self'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));
        $response->headers->remove('X-Powered-By');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return $response;
    }
}