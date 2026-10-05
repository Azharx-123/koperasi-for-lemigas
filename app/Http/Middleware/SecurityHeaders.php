<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds standard hardening headers to every response.
 *
 * The CSP below allows 'unsafe-inline' for script-src and style-src because
 * the app currently has inline <script>/<style> blocks scattered across
 * several Blade views (cart, checkout, navigation, payment confirmation,
 * product page, profile, ratings, the homepage, and a few components/error
 * pages). Going fully strict would mean adding a per-request nonce to every
 * one of those blocks — worth doing eventually, but risky to do blindly
 * without a browser to verify every page still renders correctly. Every
 * other directive here is as strict as the app's actual asset usage allows
 * (checked against what's really referenced in the views, not guessed).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Vite's dev server (`npm run dev`) serves resources/ from its own
        // origin — not the same origin as the app — so the browser needs
        // explicit permission to load from it, plus a WebSocket allowance
        // for its hot-module-reload client. This only ever applies when
        // APP_ENV=local; production keeps exactly the policy below with
        // nothing appended, since app.blade.php there is served from the
        // compiled public/build assets (same-origin, already covered by
        // 'self').
        $viteDev = 'http://127.0.0.1:5173';
        $viteDevWs = 'ws://127.0.0.1:5173';
        $isLocal = app()->environment('local');

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'" . ($isLocal ? " {$viteDev}" : ''),
"style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net" . ($isLocal ? " {$viteDev}" : ''),
"font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net" . ($isLocal ? " {$viteDev}" : ''),
            // data: covers small inline SVG/base64 assets; https: covers
            // admin-entered external image URLs (e.g. ui-avatars.com).
            "img-src 'self' data: blob: https:",
            "connect-src 'self'" . ($isLocal ? " {$viteDev} {$viteDevWs}" : ''),
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()'
        );

        // Only sent over an actual HTTPS connection — sending it over plain
        // HTTP is meaningless and can be premature if HTTPS isn't fully
        // ready yet everywhere. 1 year, gradually widen from here.
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
