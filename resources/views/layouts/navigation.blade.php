@php
    $isWelcomePage = Route::currentRouteName() === 'welcome';
    $isCartPage = Route::currentRouteName() === 'cart.show';
    $isOrdersPage = Route::currentRouteName() === 'orders.index';
@endphp

<style>
    /* Base navbar styles */
    .navbar {
        transition: background-color 0.3s ease;
        padding-top: var(--spacing-sm);
        padding-bottom: var(--spacing-sm);
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: 700;
    }

    /* Cart icon + notification-dot count badge */
    .cart-icon-wrap {
        position: relative;
        display: inline-flex;
    }

    .cart-count {
        position: absolute;
        top: -0.5rem;
        right: -0.6rem;
        font-size: 0.65rem;
        line-height: 1;
        padding: 0.3em 0.42em;
        min-width: 1.1rem;
        box-shadow: 0 0 0 2px rgb(var(--black));
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
        margin-right: var(--spacing-xs);
    }

    .navbar.fixed-top.scrolled {
        background-color: rgba(var(--black), 0.95);
        padding-top: var(--spacing-sm);
        padding-bottom: var(--spacing-sm);
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
        background-color: rgb(var(--gray-100));
    }

    .navbar .dropdown-item i {
        width: 1.25rem;
        text-align: center;
    }

    @media (max-width: 991.98px) {
        .navbar.fixed-top {
            background-color: rgba(var(--black), 0.95);
        }

        .navbar .dropdown-menu {
            background-color: transparent;
            border: none;
            box-shadow: none;
            padding-left: var(--spacing-sm);
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
        <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
            <img src="{{ \App\Helpers\ImageHelper::url($company->logo) }}" alt="Logo {{ $company->name }}" height="40" class="me-2">
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
                            <span class="cart-icon-wrap me-2">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge bg-danger rounded-pill cart-count {{ $cartCount > 0 ? '' : 'd-none' }}">{{ $cartCount }}</span>
                            </span>Keranjang
                        </a>
                    </li>
                @endif
                @auth
                    @if (!$isOrdersPage)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="fas fa-receipt me-2"></i>Pesanan
        </a>
    </li>
@endif
                @endauth

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
