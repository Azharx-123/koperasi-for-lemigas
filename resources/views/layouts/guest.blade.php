@extends('layouts.app')

@push('styles')
    <style>
        :root {
            --yellow-primary: 255, 215, 0;
            --yellow-light: 255, 229, 92;
            --yellow-dark: 178, 151, 0;
            --gray-100: 248, 249, 250;
            --gray-200: 233, 236, 239;
            --gray-300: 222, 226, 230;
            --gray-600: 108, 117, 125;
            --gray-800: 52, 58, 64;
            --black: 33, 37, 41;

            /* Variabel baru untuk ukuran font responsif */
            --font-size-base: 16px;
            --font-size-sm: 0.875rem;
            --font-size-md: 1rem;
            --font-size-lg: 1.25rem;
            --font-size-xl: 1.5rem;
            --font-size-2xl: 2rem;
            --font-size-3xl: 2.5rem;

            /* Variabel untuk spacing */
            --spacing-xs: 0.5rem;
            --spacing-sm: 1rem;
            --spacing-md: 1.5rem;
            --spacing-lg: 2rem;
            --spacing-xl: 3rem;

            /* Variabel untuk transisi */
            --transition-fast: 0.2s ease;
            --transition-medium: 0.3s ease;
            --transition-slow: 0.5s ease;

            /* Variabel untuk border-radius */
            --radius-sm: 0.25rem;
            --radius-md: 0.5rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
            --radius-full: 50%;
        }

        /* Reset dan Font Base */
        html {
            font-size: var(--font-size-base);
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: rgb(var(--black));
            overflow-x: hidden;
        }

        /* Optimasi untuk Perangkat Mobile */
        * {
            -webkit-tap-highlight-color: transparent;
            box-sizing: border-box;
        }

        /* Produk Card yang Lebih Interaktif */
         .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 250px;
            object-fit: cover;
        }

        .product-content {
            padding: 1.5rem;
        }

        .product-price {
            color: rgb(var(--yellow-dark));
            font-weight: 600;
            font-size: 1.25rem;
        }

         .product-link {
            display: flex;
            align-items: center;
            padding: clamp(1rem, 3vw, 1.5rem) clamp(1.5rem, 5vw, 3rem);
            background-color: rgba(var(--yellow-primary), 0.85);
            color: white;
            text-decoration: none;
            font-size: clamp(1.2rem, 4vw, 2rem);
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .product-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgb(var(--yellow-dark));
            z-index: -1;
            transform: translateY(100%);
            transition: transform 0.4s ease;
        }

        .product-link:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .product-link:hover::before {
            transform: translateY(0);
        }

        .animated-arrows {
            display: flex;
            align-items: center;
            margin-left: clamp(0.8rem, 2vw, 1.5rem);
        }

        .arrow {
            width: clamp(15px, 4vw, 30px);
            height: clamp(15px, 4vw, 30px);
            border-top: 4px solid white;
            border-right: 4px solid white;
            transform: rotate(45deg);
            margin-left: -15px;
            animation: arrowPulse 2s infinite;
            will-change: opacity;
        }

        .arrow-second {
            margin-left: -5px;
            animation-delay: 0.5s;
        }

        @keyframes arrowPulse {
            0% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.3;
            }
        }

        /* Animasi untuk Elemen Halaman saat Scroll */
        [data-aos] {
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, opacity;
        }

        [data-aos="fade-down"] {
            transform: translateY(-30px);
        }

        [data-aos="fade-up"] {
            transform: translateY(30px);
        }

        [data-aos="fade-right"] {
            transform: translateX(-30px);
        }

        [data-aos="fade-left"] {
            transform: translateX(30px);
        }

        [data-aos="zoom-in"] {
            transform: scale(0.9);
        }

        [data-aos].aos-animate {
            opacity: 1;
            transform: translate(0) scale(1);
        }

        /* Efek Hover Khusus untuk Desktop */
        @media (hover: hover) {

            .product-card:hover,
            .persona-card:hover,
            .feature-box:hover,
            .lemigas-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            }

            .btn-view-more:hover,
            .view-more-btn:hover,
            .closing-cta:hover,
            .product-link:hover {
                transform: translateY(-5px);
            }
        }

        /* Media Queries Optimal */
        /* Tablet dan Perangkat Medium */
        @media (max-width: 992px) {
            .closing-grid {
                grid-template-columns: 1fr;
                gap: clamp(1.5rem, 4vw, 2rem);
            }

            .closing-text {
                padding-right: 0;
                text-align: center;
            }

            .closing-heading {
                font-size: clamp(1.8rem, 4.5vw, 2.2rem);
            }

            .hero-banner {
                height: auto;
                min-height: 100vh;
            }

            .hero-text-container {
                padding-left: clamp(1rem, 5vw, 2rem);
            }
        }

        /* Mobile dan Perangkat Kecil */
        @media (max-width: 768px) {
            :root {
                --font-size-base: 15px;
            }

            .carousel-item {
                height: calc(100vh - 56px);
            }

            .carousel-caption h2 {
                font-size: clamp(1.5rem, 7vw, 1.8rem);
            }

            .carousel-caption p {
                font-size: clamp(0.9rem, 3vw, 1rem);
            }

            .section-elegant,
            .lemigas-section {
                padding: clamp(40px, 8vw, 60px) 0;
            }

            .hero-banner::after {
                display: none;
            }

            .hero-banner {
                background-attachment: scroll;
            }

            .hero-text-container {
                padding: clamp(4rem, 10vh, 6rem) clamp(1rem, 5vw, 2rem);
                text-align: center;
            }

            .hero-title {
                font-size: clamp(1.5rem, 7vw, 3rem);
                border-left: none;
                padding-left: 0;
                padding-bottom: clamp(0.5rem, 2vw, 1rem);
                border-bottom: 4px solid rgb(var(--yellow-primary));
            }

            .hero-banner:hover .hero-title {
                padding-left: 0;
            }

            .hero-description {
                max-width: 100%;
            }

            .btn-view-more {
                display: block;
                margin: 0 auto;
                width: fit-content;
            }

            .hero-action-container {
                padding: clamp(1rem, 5vh, 3rem) clamp(1rem, 5vw, 2rem) clamp(4rem, 10vh, 6rem);
            }

            .product-link {
                width: 100%;
                justify-content: center;
            }

            .lemigas-features {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .lemigas-image {
                height: clamp(200px, 35vh, 250px);
            }

            .closing-image-container {
                height: clamp(250px, 40vh, 300px);
                margin: 0 auto;
            }

            .closing-person-image {
                position: relative;
                margin: 0 auto;
                left: 0;
                right: 0;
            }
        }

        /* Fallback untuk perangkat yang tidak mendukung fixed attachment */
        @media (max-width: 768px),
        (hover: none) {
            .hero-banner {
                background-attachment: scroll;
            }
        }

        /* Small Phones */
        @media (max-width: 576px) {
            :root {
                --font-size-base: 14px;
            }

            .persona-section::before,
            .persona-section::after {
                display: none;
            }

            .persona-card,
            .product-card,
            .feature-box {
                padding: clamp(1rem, 4vw, 1.25rem);
                margin-bottom: clamp(1rem, 3vw, 1.5rem);
            }

            .feature-icon,
            .persona-icon-container {
                width: clamp(45px, 12vw, 60px);
                height: clamp(45px, 12vw, 60px);
                margin-bottom: clamp(0.8rem, 2vw, 1rem);
            }

            .feature-title,
            .persona-title {
                font-size: clamp(1rem, 4.5vw, 1.1rem);
            }

            .persona-counter {
                font-size: clamp(1.3rem, 6vw, 1.6rem);
            }

            .closing-heading {
                font-size: clamp(1.5rem, 7vw, 2rem);
            }

            .closing-description {
                font-size: clamp(0.875rem, 3.5vw, 1rem);
            }

            .closing-cta {
                width: 100%;
                justify-content: center;
            }

            .view-more-btn {
                width: 100%;
                justify-content: center;
            }

            .carousel-indicators {
                bottom: 1rem;
            }

            .carousel-caption {
                bottom: 15%;
            }
        }

        /* Aksesibilitas */
        .btn-view-more:focus,
        .view-more-btn:focus,
        .product-link:focus,
        .closing-cta:focus,
        .scroll-top-btn:focus {
            outline: 2px solid rgb(var(--yellow-primary));
            outline-offset: 2px;
        }

        /* Dukungan Aksesibilitas: High Contrast Mode */
        @media (prefers-contrast: high) {
            :root {
                --yellow-primary: 204, 170, 0;
                --yellow-light: 204, 170, 0;
                --yellow-dark: 153, 128, 0;
            }

            .elegant-separator,
            .lemigas-separator,
            .lemigas-content::before,
            .persona-card::after,
            .product-content::after {
                background: rgb(var(--yellow-dark));
            }

            .hero-banner::before,
            .closing-overlay,
            .carousel-item::after {
                background: rgba(0, 0, 0, 0.8);
            }
        }

        /* Dukungan Dark Mode */


        /* Prefers Reduced Motion */
        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }

            .loading-screen {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Best Products Section -->
    <section class="section-elegant" data-aos="fade-up" id="products-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Produk Unggulan Kami</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="row g-4">
                @foreach ($best_products as $product)
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 200 }}">
                        <div class="product-card">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="product-image w-100"
                                    alt="{{ $product->name }}" loading="lazy">
                            @endif
                            <div class="product-content">
                                <h5 class="mb-3">{{ $product->name }}</h5>
                                <p class="text-muted mb-3">{{ $product->short_description }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">{{ $product->formatted_price }}</span>
                                    <span class="badge bg-dark">{{ $product->sold_count }}+ Terjual</span>
                                </div>
                                <a href="{{ route('products.show', $product->slug) }}"
                                    class="btn btn-outline-dark w-100 mt-3">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View More Button Container -->
            <div class="view-more-container" data-aos="fade-up" data-aos-delay="300">
                <a href="{{ route('products.index') }}" class="view-more-btn">
                    <span class="view-more-text">Lihat Semua Produk</span>
                    <span class="view-more-icon">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>


    <!-- Persona Section -->
    <section class="section-elegant persona-section" data-aos="fade-up" id="persona-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Siapa yang Cocok dengan Produk Kami?</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="100"></div>
            <div class="row g-4">
                @foreach ($personas as $persona)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="persona-card h-100">
                            <div class="persona-icon-container">
                                <div class="persona-icon-bg"></div>
                                <div class="persona-icon">
                                    <i class="{{ $persona->icon }} fa-lg"></i>
                                </div>
                            </div>
                            <span class="persona-counter" data-count="{{ rand(100, 500) }}">0+</span>
                            <h4 class="persona-title">{{ $persona->title }}</h4>
                            <p class="persona-subtitle">{{ $persona->subtitle }}</p>
                            <p class="persona-description">{{ $persona->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() 

            // Lazy load gambar untuk meningkatkan performa
            const lazyLoadImages = () => {
                if ('loading' in HTMLImageElement.prototype) {
                    // Gunakan native lazy loading jika browser mendukung
                    const images = document.querySelectorAll('img[loading="lazy"]');
                    images.forEach(img => {
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                    });
                } else {
                    // Fallback untuk browser yang tidak mendukung
                    const lazyImages = document.querySelectorAll('img[data-src]');
                    const imageObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                imageObserver.unobserve(img);
                            }
                        });
                    });

                    lazyImages.forEach(img => {
                        imageObserver.observe(img);
                    });
                }
            };

            // Inisialisasi semua fungsi
            setTimeout(() => {
                initSponsors();
                initCounters();
                initMagneticEffect();
                lazyLoadImages();
            }, 100);

            // Animasi gambar carousel
            const carouselItems = document.querySelectorAll('.carousel-item');
            carouselItems.forEach(item => {
                item.addEventListener('transitionend', function(e) {
                    if (e.target === this && this.classList.contains('active')) {
                        const img = this.querySelector('img');
                        if (img) {
                            // Hanya animate gambar saat slide menjadi aktif
                            img.style.transition = 'transform 8s ease';
                            img.style.transform = 'scale(1.08)';
                        }
                    }
                });
            });

            // Perbaikan scroll smooth untuk link anchor
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href');
                    if (targetId !== '#' && document.querySelector(targetId)) {
                        e.preventDefault();
                        document.querySelector(targetId).scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
@endpush
