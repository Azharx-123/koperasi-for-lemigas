<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $company->name }}</title>

    <!-- Styles -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333333;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        letter-spacing: -0.02em;
        }

        p {
            font-family: 'Inter', sans-serif;
        }

        .lead {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            line-height: 1.8;
        }

        /* Section Headers */
        .display-4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
        }
    </style>
    @stack('styles')
</head>

<body>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                mirror: true, // Enable reverse animations
                once: false, // Whether animation should happen only once
                offset: 120, // Offset (in px) from the original trigger point
                duration: 1000, // Duration of animation
                easing: 'ease-in-out', // Default easing for AOS animations
                anchorPlacement: 'top-bottom', // Defines which position of the element regarding to window should trigger the animation
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
