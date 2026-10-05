import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/welcome.css',
                'resources/css/pages/layouts-guest.css',
                'resources/css/pages/auth-forgot-password.css',
                'resources/css/pages/auth-register.css',
                'resources/css/pages/auth-login.css',
                'resources/css/pages/orders-payment-confirmation.css',
                'resources/css/pages/orders-index.css',
                'resources/css/pages/orders-show.css',
                'resources/css/pages/profile-edit.css',
                'resources/css/pages/checkout-index.css',
                'resources/css/pages/checkout-complete.css',
                'resources/css/pages/cart-show.css',
                'resources/css/pages/products-index.css',
                'resources/css/pages/products-show.css',
                'resources/css/pages/about.css',
                'resources/css/pages/ratings-index.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        // Pinned to IPv4 loopback so the dev server always has a
        // predictable origin (http://127.0.0.1:5173). Without this, Vite
        // resolves "localhost" using the OS's DNS order, which on some
        // machines returns the IPv6 loopback [::1] instead — that's where
        // the http://[::1]:5173 origin in the CSP console errors came
        // from. Keeping this fixed also lets SecurityHeaders.php allow an
        // exact origin instead of a wildcard.
        host: '127.0.0.1',
        port: 5173,
    },
});
