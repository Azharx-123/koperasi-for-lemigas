<?php

use App\Http\Middleware\CanonicalHost;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(
            prepend: [
                CanonicalHost::class,
            ],
            append: [
                SecurityHeaders::class,
            ],
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Human, Indonesian messages for JSON/AJAX requests that hit a
        // guarded route while logged out, forbidden, pointed at something
        // that no longer exists, or carrying an expired CSRF token —
        // instead of Laravel's raw default wording ("Unauthenticated.",
        // "This action is unauthorized.", "CSRF token mismatch.", or a
        // "No query results for model [App\Models\X] 5" class name)
        // landing straight in a page's toast/alert. The add-to-cart
        // "Produk gagal ditambahkan" notice on the product page was the
        // one actually reported (a logged-out add-to-cart hit the first
        // case below), but all four are the same category of raw system
        // text rather than something written for a shopper to read, so
        // all four get the same treatment here instead of being patched
        // one-by-one wherever each happens to surface.
        //
        // Every callback bails out (returns null, so Laravel's normal
        // handling takes over) for /admin/*, so Filament keeps rendering
        // its own panel's errors exactly as before, and for non-JSON
        // requests, which already get Laravel's normal redirect-to-login
        // (401) or this app's own resources/views/errors/{403,404,419}
        // pages — both already friendly before this change.
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('admin', 'admin/*') || ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Silakan login terlebih dahulu untuk melanjutkan.',
            ], 401);
        });

        // Laravel converts a policy/Gate denial (AuthorizationException
        // with no custom status) to AccessDeniedHttpException before any
        // render() callback runs, so that's the type that actually has to
        // be matched here — a callback typed to AuthorizationException
        // itself would simply never fire.
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('admin', 'admin/*') || ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
            ], 403);
        });

        // Same reasoning: Laravel converts ModelNotFoundException (e.g. an
        // AJAX call for a product that was since deleted) to
        // NotFoundHttpException before render() callbacks run.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('admin', 'admin/*') || ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Data yang Anda cari tidak ditemukan atau sudah dihapus.',
            ], 404);
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            // TokenMismatchException (an expired CSRF token — e.g. a
            // checkout form left open long enough for the session to
            // time out) is converted to a plain HttpException(419, ...)
            // before render() callbacks run, so status code is the only
            // way left to identify it here. Any other HttpException
            // status falls through to Laravel's normal handling.
            if ($e->getStatusCode() !== 419 || $request->is('admin', 'admin/*') || ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.',
            ], 419);
        });
    })->create();
