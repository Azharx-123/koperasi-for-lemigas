<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Company;
use App\Services\GuestCart;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share company data (and cart item count) with every view that
        // references $company/$cartCount directly in its own template,
        // not just layouts.app.
        //
        // Scoping to layouts.app alone would miss welcome/about/auth.login/
        // auth.register/auth.forgot-password: those @extend('layouts.app'),
        // and Blade's @extends compiles to "render my own @section content
        // first, then make() the parent layout" — so layouts.app's composer
        // fires too late to help a child view that touches $company while
        // building its own @section('content'). Every view below is listed
        // because it references $company (or, for the cart badge, needs
        // $cartCount) directly — layouts.navigation and layouts.footer are
        // deliberately NOT listed even though they use $company: both are
        // only ever @include()'d from inside layouts.app's own render pass
        // (after layouts.app's composer has already run), so they inherit
        // it automatically.
        //
        // checkout.index, checkout.complete, and orders.payment_confirmation
        // are here for the same reason: each references $company directly
        // (bank transfer details) in its own @section('content'), same as
        // welcome/about/the auth pages. products.index joins them for the
        // same reason too, but via its @section('meta_description', '...'
        // . $company->name . '...') SEO tag — easy to miss since it's a
        // one-line @section call rather than a visible chunk of markup.
        //
        // Still scoped to this explicit list (not View::share, which is
        // truly global) so this never fires on Filament's admin-panel views.
        View::composer(
            [
                'layouts.app', 'welcome', 'about',
                'auth.login', 'auth.register', 'auth.forgot-password',
                'checkout.index', 'checkout.complete', 'orders.payment_confirmation',
                'products.index',
            ],
            function ($view) {
                $view->with('company', cache()->remember('company_info', now()->addDay(), function () {
                    // Falls back to an empty (never-saved) Company instance rather
                    // than null when no row exists yet — e.g. right after a fresh
                    // install/migrate, before an admin has filled in the company
                    // profile via Filament, or in a test's RefreshDatabase schema.
                    // Every view that reads $company->name/address/logo/etc.
                    // (footer, navigation, welcome, about, checkout...) dereferences
                    // it directly, so a null here fatals the very first page load
                    // instead of just rendering blank fields.
                    return Company::first() ?? new Company();
                }));

                // #10: cabang tamu (checkout tanpa akun) baca dari GuestCart
                // (session), bukan lagi selalu 0 — supaya badge keranjang di
                // navbar tetap benar buat pengunjung yang belum login.
                $view->with('cartCount', auth()->check()
                    ? (auth()->user()->cart?->getItemCount() ?? 0)
                    : app(GuestCart::class)->count());
            }
        );
    }
}
