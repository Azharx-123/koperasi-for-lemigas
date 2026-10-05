<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $__env->hasSection('title') ? $__env->yieldContent('title') . ' - ' . $company->name : $company->name }}</title>
    <meta name="description" content="{{ $__env->yieldContent('meta_description', \Illuminate\Support\Str::limit($company->description ?? $company->name, 160)) }}">

    <!-- Open Graph / WhatsApp & social link previews -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $company->name }}">
    <meta property="og:title" content="{{ $__env->hasSection('title') ? $__env->yieldContent('title') . ' - ' . $company->name : $company->name }}">
    <meta property="og:description" content="{{ $__env->yieldContent('meta_description', \Illuminate\Support\Str::limit($company->description ?? $company->name, 160)) }}">
    <meta property="og:image" content="{{ $__env->yieldContent('meta_image', \App\Helpers\ImageHelper::url($company->logo)) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon-32x32.png') }}" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon-16x16.png') }}" type="image/png" sizes="16x16">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @stack('styles')
</head>

<body>
    <!-- Page loading screen. Hidden as soon as the page has finished
         loading (see inline script just below), and shown again briefly
         on the next outgoing navigation (see resources/js/app.js). Kept as
         plain inline markup + a tiny inline script — not part of the Vite
         bundle — so it doesn't depend on the very JS/CSS that might still
         be loading, and a <noscript> rule guarantees it can never trap the
         page if JS is disabled. -->
    <div id="page-loader" class="page-loader" aria-hidden="true">
        <div class="page-loader-inner">
            @if (!empty($company->logo))
                <img src="{{ \App\Helpers\ImageHelper::url($company->logo) }}" alt="" class="page-loader-logo">
            @endif
            <div class="page-loader-spinner"></div>
        </div>
    </div>
    <noscript>
        <style>#page-loader { display: none !important; }</style>
    </noscript>
    <script>
        (function () {
            var loader = document.getElementById('page-loader');
            if (!loader) return;

            var shownAt = Date.now();
            var hidden = false;

            function hide() {
                if (hidden) return;
                hidden = true;
                // Keep it visible at least briefly so it doesn't just flash
                // on fast/cached loads — long enough to read as intentional,
                // short enough to not feel like it's stalling anything.
                var wait = Math.max(0, 250 - (Date.now() - shownAt));
                setTimeout(function () {
                    loader.classList.add('page-loader-hidden');
                }, wait);
            }

            if (document.readyState === 'complete') {
                hide();
            } else {
                window.addEventListener('load', hide);
            }

            // Safety net: never block the page for more than a few seconds
            // no matter what else goes wrong.
            setTimeout(hide, 6000);
        })();
    </script>

    <!-- Navbar -->
    @if (!Request::is('login') && !Request::is('register') && !Request::is('forgot-password'))
        @include('layouts.navigation')
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @if (!Request::is('login') && !Request::is('register') && !Request::is('forgot-password'))
        @include('layouts.footer')
    @endif

    <!-- Scripts -->
    @stack('scripts')
</body>

</html>
