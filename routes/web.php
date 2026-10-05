<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Second step of admin login (see App\Filament\Auth\Login::authenticate()).
// Deliberately outside the 'auth' group — the user is logged out again
// while this step is pending, by design.
Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'show'])->name('two-factor.challenge');
Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'verify'])
    ->name('two-factor.verify')
    ->middleware('throttle:5,1');

Route::get('/dashboard', function () {
    return redirect()->route('orders.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/ratings', [RatingController::class, 'index'])->name('ratings.index');

// Keranjang & checkout SENGAJA di luar grup middleware 'auth' (lihat #10 di
// CATATAN-LANJUTAN-REVISI.md) — checkout tanpa akun (tamu) adalah salah satu
// fitur utama toko ini. Kepemilikan per-order untuk tamu dijaga lewat
// session('guest_order_ids') + OrderPolicy (bukan lewat middleware), karena
// "siapa boleh lihat order yang mana" itu aturan yang beda dari sekadar
// "harus login atau tidak" begitu tamu diizinkan checkout — lihat
// OrderPolicy::view()/cancel() dan CheckoutController::complete().
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/complete/{order}', [CheckoutController::class, 'complete'])->name('checkout.complete');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{order}/payment-confirmation', [OrderController::class, 'showPaymentConfirmation'])->name('orders.payment.confirmation');

Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add')->middleware('throttle:30,1');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove')->middleware('throttle:30,1');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update')->middleware('throttle:30,1');

Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process')->middleware('throttle:10,1');
Route::post('/orders/{order}/payment-confirmation', [OrderController::class, 'processPaymentConfirmation'])->name('orders.payment.process')->middleware('throttle:10,1');
Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel')->middleware('throttle:10,1');

Route::middleware('auth')->group(function () {
    // Read-only pages: no throttle needed, browsing/pagination shouldn't
    // ever hit a rate limit. Riwayat pesanan (orders.index) & profil tetap
    // fitur khusus akun — tamu tidak punya akun untuk didaftar riwayatnya.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    // Routine write actions: generous limit that no real user browsing the
    // site would ever hit, but still caps a script hammering the endpoint.
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('throttle:30,1');

    // Sensitive write actions: these move stock/money, change order state,
    // or are irreversible, so they get a tighter limit than routine cart
    // actions — same 10/minute tier regardless of guessed vs. legitimate
    // use, since no real checkout/cancel/review flow needs more than that.
    Route::post('/products/{product}/ratings', [RatingController::class, 'store'])->name('ratings.store')->middleware('throttle:10,1');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('throttle:10,1');
});

require __DIR__ . '/auth.php';
