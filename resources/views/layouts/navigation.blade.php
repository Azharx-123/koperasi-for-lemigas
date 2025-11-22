@php
    $isWelcomePage = Route::currentRouteName() === 'welcome';
    $isCartPage = Route::currentRouteName() === 'cart.show';
@endphp

<style>
    /* Base navbar styles */
    .navbar {
        transition: background-color 0.3s ease;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .nav-link {
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
        color: white;
    }

    /* Icon styling */
    .nav-link i {
        width: 1.25rem;
        text-align: center;
        margin-right: 0.5rem;
    }

    .navbar.fixed-top.scrolled {
        background-color: rgba(33, 37, 41, 0.95);
        padding-top: 1rem;
        padding-bottom: 1rem;
        backdrop-filter: blur(10px);
    }

    .navbar .dropdown-menu {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .navbar .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .navbar .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .navbar .dropdown-item i {
        width: 1.25rem;
        text-align: center;
    }

    @media (max-width: 991.98px) {
        .navbar.fixed-top {
            background-color: rgba(33, 37, 41, 0.95);
        }

        .navbar .dropdown-menu {
            background-color: transparent;
            border: none;
            box-shadow: none;
            padding-left: 1rem;
        }

        .navbar .dropdown-item {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.5rem 0;
        }

        .navbar .dropdown-item:hover {
            background-color: transparent;
            color: #fff;
        }
    }
</style>

<nav class="navbar navbar-expand-lg {{ $isWelcomePage ? 'navbar-dark fixed-top' : 'navbar-dark bg-dark' }}">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ Storage::url($company->logo) }}" alt="Logo {{ $company->name }}" height="40" class="me-2">
            {{ $company->name }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('welcome') }}">
                        <i class="fas fa-home me-2"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">
                        <i class="fas fa-info-circle me-2"></i>Tentang Kami
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">
                        <i class="fas fa-box me-2"></i>Produk
                    </a>
                </li>
                @if (!$isCartPage)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.show') }}">
                            <i class="fas fa-shopping-cart me-2"></i>Keranjang
                        </a>
                    </li>
                @endif

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i>Profil Saya
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar.fixed-top');
        if (navbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }
    });
</script>
