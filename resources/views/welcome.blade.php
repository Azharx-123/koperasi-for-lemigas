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
            line-height: 1.6;
            color: rgb(var(--black));
            overflow-x: hidden;
        }

        /* Optimasi untuk Perangkat Mobile */
        * {
            -webkit-tap-highlight-color: transparent;
            box-sizing: border-box;
        }

        /* Loading Screen yang Lebih Efisien */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.25s ease-out;
            will-change: opacity;
            /* Optimalkan performa animasi */
        }

        .loading-screen.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        .logo-container {
            margin-bottom: var(--spacing-lg);
            transform: scale(0);
            animation: logoReveal 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            will-change: transform, opacity;
            /* Optimalkan performa animasi */
        }

        .company-logo {
            width: clamp(120px, 15vw, 200px);
            /* Responsif */
            height: auto;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.1));
        }

        .loading-bar {
            width: clamp(180px, 40vw, 250px);
            /* Responsif */
            height: 3px;
            background-color: #eee;
            border-radius: var(--radius-sm);
            overflow: hidden;
            opacity: 0;
            animation: barAppear 0.25s ease forwards 0.4s;
            will-change: opacity, transform;
            /* Optimalkan performa animasi */
        }

        .loading-progress {
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgb(var(--yellow-primary)), rgb(var(--yellow-dark)));
            transform: translateX(-100%);
            animation: loadProgress 0.8s ease-in-out forwards 0.65s;
            will-change: transform;
            /* Optimalkan performa animasi */
        }

        @keyframes logoReveal {
            0% {
                transform: scale(0) rotate(-5deg);
                opacity: 0;
            }

            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes barAppear {
            0% {
                opacity: 0;
                transform: scaleX(0.3);
            }

            100% {
                opacity: 1;
                transform: scaleX(1);
            }
        }

        @keyframes loadProgress {
            0% {
                transform: translateX(-100%);
            }

            70% {
                transform: translateX(-5%);
            }

            100% {
                transform: translateX(0);
            }
        }

        /* Tombol Scroll to Top yang Lebih Responsif */
        .scroll-top-btn {
            position: fixed;
            bottom: clamp(15px, 4vw, 30px);
            right: clamp(15px, 4vw, 30px);
            width: clamp(40px, 12vw, 50px);
            height: clamp(40px, 12vw, 50px);
            border-radius: var(--radius-full);
            background-color: rgb(var(--yellow-primary));
            color: rgb(0, 0, 0);
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transform: translateY(20px) scale(0.9);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            will-change: transform, opacity;
        }

        .scroll-top-btn.visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .scroll-top-btn:hover,
        .scroll-top-btn:focus {
            background-color: rgb(var(--yellow-dark));
            transform: translateY(-5px) scale(1.05);
            outline: none;
        }

        .scroll-top-btn:active {
            transform: translateY(0) scale(0.95);
        }

        .scroll-top-btn i {
            font-size: clamp(0.9rem, 2.5vw, 1.2rem);
        }

        /* Carousel yang Lebih Responsif dan Imersif */
        .carousel-item {
            height: 100vh;
            max-height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transform: scale(1.02);
            /* Sedikit memperbesar untuk menghindari border putih saat animasi */
            transition: transform 8s ease;
            /* Slow zoom effect saat slide aktif */
        }

        .carousel-item.active img {
            transform: scale(1.08);
        }

        .carousel-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0.6) 0%,
                    rgba(0, 0, 0, 0.4) 40%,
                    rgba(0, 0, 0, 0.4) 60%,
                    rgba(0, 0, 0, 0.6) 100%);
            z-index: 1;
        }

        /* Carousel Captions Lebih Menarik dan Responsif */
        .carousel-caption {
            bottom: 20%;
            z-index: 2;
            padding: 0 max(5%, 15px);
            text-align: center;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease, transform 1s ease;
        }

        .carousel-item.active .carousel-caption {
            opacity: 1;
            transform: translateY(0);
        }

        .carousel-caption h2 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.8rem, 5vw, 3.5rem);
            font-weight: 700;
            text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.7);
            letter-spacing: -0.03em;
            margin-bottom: clamp(0.8rem, 2vw, 1.5rem);
        }

        .carousel-caption p {
            font-family: 'Inter', sans-serif;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            font-weight: 400;
            text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.7);
            letter-spacing: 0.2px;
            max-width: 800px;
            margin: 0 auto;
        }

        .carousel-indicators {
            bottom: clamp(30px, 10vh, 100px);
            margin-bottom: 0;
        }

        .carousel-indicators button {
            width: clamp(10px, 2vw, 15px);
            height: clamp(10px, 2vw, 15px);
            background-color: rgba(255, 255, 255, 0.5);
            margin: 0 clamp(4px, 1vw, 8px);
            border: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            transform: scale(0.8);
        }

        .carousel-indicators button.active {
            background-color: rgb(var(--yellow-primary));
            transform: scale(1);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 10%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .carousel:hover .carousel-control-prev,
        .carousel:hover .carousel-control-next {
            opacity: 0.7;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            opacity: 1;
        }

        /* Light Separator yang Lebih Efisien */
        .light-separator {
            position: relative;
            height: 8px;
            background: #000000;
            overflow: hidden;
            z-index: 10;
        }

        .light-beam {
            position: absolute;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 0, 0) 0%,
                    rgba(255, 229, 92, 1) 50%,
                    rgba(255, 255, 0, 0) 100%);
            animation: lightMove 3s linear infinite;
            transform-origin: center;
            will-change: transform, opacity;
        }

        .light-beam-left {
            left: 0;
            transform: translateX(-100%);
        }

        .light-beam-right {
            right: 0;
            transform: translateX(100%);
        }

        @keyframes lightMove {
            0% {
                transform: translateX(0);
                opacity: 0;
            }

            25% {
                opacity: 1;
            }

            50% {
                transform: translateX(-100%);
                opacity: 0;
            }

            51% {
                transform: translateX(100%);
            }

            75% {
                opacity: 1;
            }

            100% {
                transform: translateX(0);
                opacity: 0;
            }
        }

        /* Separator yang Lebih Elegan */
        .elegant-separator {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgb(var(--yellow-primary)) 50%, transparent 100%);
            width: clamp(100px, 20vw, 150px);
            margin: clamp(1.5rem, 4vw, 2rem) auto;
        }

        /* Section Styles yang Lebih Konsisten */
        .section-elegant {
            padding: clamp(60px, 10vw, 100px) 0;
            position: relative;
            overflow: hidden;
        }

        .section-title-elegant {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 600;
            color: rgb(var(--black));
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            position: relative;
            text-align: center;
        }

        .bg-dark .section-title-elegant {
            color: white;
        }

        /* About KEP Section yang Lebih Menarik */
        .kep-section {
            padding: clamp(60px, 10vw, 100px) 0;
            background-color: rgb(var(--gray-100));
        }

        .kep-title {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 600;
            text-align: center;
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
        }

        .kep-separator {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgb(var(--yellow-primary)) 50%, transparent 100%);
            width: clamp(100px, 20vw, 150px);
            margin: 0 auto clamp(1.5rem, 3vw, 3rem);
        }

        .kep-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            transition: transform 0.5s ease, box-shadow 0.5s ease;
        }

        .kep-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .kep-image {
            height: clamp(250px, 40vh, 400px);
            position: relative;
            overflow: hidden;
        }

        .kep-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .kep-card:hover .kep-image img {
            transform: scale(1.08);
        }

        .kep-content {
            padding: clamp(1.5rem, 5vw, 3rem);
            position: relative;
        }

        .kep-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: clamp(50px, 8vw, 80px);
            height: 4px;
            background: rgb(var(--yellow-primary));
            border-radius: 2px;
        }

        .kep-description {
            font-size: clamp(1rem, 2vw, 1.1rem);
            line-height: 1.8;
            color: rgb(var(--gray-600));
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            text-align: center;
        }

        .kep-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: clamp(1rem, 3vw, 2rem);
            margin-top: clamp(2rem, 4vw, 3rem);
        }

        .feature-box {
            text-align: center;
            padding: clamp(1.5rem, 3vw, 2rem);
            background: rgb(var(--gray-100));
            border-radius: var(--radius-md);
            transition: all 0.4s ease;
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            background: rgb(var(--gray-800));
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .feature-icon {
            width: clamp(50px, 10vw, 70px);
            height: clamp(50px, 10vw, 70px);
            background: rgb(var(--yellow-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto clamp(1rem, 2vw, 1.5rem);
            transition: transform 0.4s ease, background-color 0.4s ease;
        }

        .feature-box:hover .feature-icon {
            transform: rotate(5deg) scale(1.1);
            background: rgb(var(--yellow-primary));
        }

        .feature-title {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.1rem, 2.5vw, 1.25rem);
            font-weight: 600;
            margin-bottom: clamp(0.5rem, 1.5vw, 1rem);
            color: rgb(var(--gray-800));
        }

        .feature-text {
            color: rgb(var(--gray-600));
            line-height: 1.6;
            font-size: clamp(0.9rem, 1.8vw, 1rem);
        }

        /* Hero Banner yang Lebih Imersif */
        .hero-banner {
            position: relative;
            background-size: cover;
            background-position: center 50%;
            /* Posisi awal yang konsisten */
            background-attachment: fixed;
            /* Alternatif cara untuk parallax */
            height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            overflow: hidden;
            will-change: background-position;
            /* Optimalkan performa rendering */
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 100%);
            z-index: 1;
            transition: background 0.5s ease;
        }

        .hero-banner:hover::before {
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 100%);
        }

        .hero-content {
            position: relative;
            width: 100%;
            z-index: 2;
        }

        .hero-text-container {
            padding: clamp(2rem, 5vw, 4rem) 0 clamp(2rem, 5vw, 4rem) clamp(1rem, 5vw, 4rem);
            padding-left: max(20%, 1rem);
        }

        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 700;
            margin-bottom: clamp(1rem, 3vw, 2rem);
            line-height: 1.1;
            position: relative;
            padding-left: clamp(1rem, 3vw, 2rem);
            border-left: 4px solid rgb(var(--yellow-primary));
            transition: padding-left 0.3s ease;
        }

        .hero-banner:hover .hero-title {
            padding-left: clamp(1.5rem, 3.5vw, 2.5rem);
        }

        .hero-description {
            font-size: clamp(1rem, 2.5vw, 1.2rem);
            max-width: 100%;
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            line-height: 1.8;
        }

        .btn-view-more {
            display: inline-block;
            padding: 0.5rem 0;
            color: white;
            text-decoration: none;
            font-size: clamp(0.8rem, 1.8vw, 0.9rem);
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .btn-view-more::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: rgb(var(--yellow-primary));
            transform-origin: right;
            transform: scaleX(1);
            transition: transform 0.3s ease;
        }

        .btn-view-more:hover {
            transform: translateY(-2px);
            color: rgb(var(--yellow-light));
        }

        .btn-view-more:hover::after {
            transform-origin: left;
            transform: scaleX(1.2);
        }

        .hero-action-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Produk Card yang Lebih Interaktif */
        .product-card {
            background: white;
            border-radius: 8px;
            /* Less rounded corners for professional look */
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            /* Lighter shadow */
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .product-image {
            height: 160px;
            /* Reduced height */
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-content {
            padding: 0.75rem;
            /* Smaller padding */
            flex: 1;
            display: flex;
            flex-direction: column;
            border-top: 1px solid rgba(var(--gray-200), 1);
            /* Subtle separator */
        }

        .product-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Reduced to 2 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.8em;
            /* About 2 lines of text */
            margin-bottom: 0.3rem;
            /* Smaller margin */
            font-size: 0.85rem;
            /* Smaller font */
            line-height: 1.4;
            font-weight: 600;
            /* Make title stand out */
            color: rgb(var(--gray-800));
        }

        .product-description {
            min-height: 3em;
            /* About 2 lines */
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            /* Smaller font */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: rgb(var(--gray-600));
            /* Lighter color for description */
        }

        .product-price {
            color: rgb(var(--yellow-dark));
            font-weight: 600;
            font-size: 0.9rem;
            /* Slightly smaller */
        }

        .product-footer {
            margin-top: auto;
            font-size: 0.8rem;
            /* Smaller font */
            padding-top: 0.5rem;
        }

        .product-badge {
            display: inline-block;
            padding: 0.2em 0.4em;
            font-size: 0.65em;
            font-weight: 600;
            line-height: 1;
            color: white;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            /* Less rounded */
            background-color: rgba(var(--gray-800), 0.9);
            /* Slightly lighter */
        }

        .product-detail-btn {
            display: block;
            text-align: center;
            padding: 0.4rem 0;
            /* Smaller padding */
            margin-top: 0.5rem;
            background: transparent;
            color: rgb(var(--gray-800));
            border: 1px solid rgb(var(--gray-300));
            /* Thinner border */
            border-radius: 4px;
            /* More subtle rounded corners */
            font-weight: 500;
            font-size: 0.75rem;
            /* Smaller font */
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .product-detail-btn:hover {
            background: rgb(var(--yellow-primary));
            border-color: rgb(var(--yellow-primary));
            color: rgb(var(--black));
            transform: translateY(-2px);
            /* Smaller hover effect */
            box-shadow: 0 3px 10px rgba(var(--yellow-primary), 0.15);
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

        /* Tombol View More yang Lebih Menarik */
        .view-more-container {
            text-align: center;
            margin-top: clamp(2rem, 4vw, 3rem);
            padding: clamp(0.5rem, 1.5vw, 1rem);
        }

        .view-more-btn {
            display: inline-flex;
            align-items: center;
            padding: clamp(0.8rem, 2vw, 1rem) clamp(1.5rem, 3vw, 2.5rem);
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(var(--yellow-primary), 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .view-more-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgb(var(--yellow-primary)), rgb(var(--yellow-dark)));
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: -1;
        }

        .view-more-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .view-more-btn:hover::before {
            opacity: 1;
        }

        .view-more-btn:active {
            transform: translateY(0) scale(0.98);
        }

        .view-more-text {
            position: relative;
            z-index: 1;
            margin-right: clamp(0.5rem, 1.5vw, 1rem);
        }

        .view-more-icon {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: clamp(20px, 4vw, 24px);
            height: clamp(20px, 4vw, 24px);
            transition: transform 0.4s ease;
        }

        .view-more-btn:hover .view-more-icon {
            transform: translateX(8px);
        }

        /* Persona Card yang Lebih Interaktif */
        .persona-section {
            padding: clamp(40px, 8vw, 60px) 0;
            position: relative;
            overflow: hidden;
        }

        .persona-section::before,
        .persona-section::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            z-index: 0;
            filter: blur(50px);
            will-change: transform;
        }

        .persona-section::before {
            top: -50px;
            right: -50px;
            width: clamp(150px, 25vw, 200px);
            height: clamp(150px, 25vw, 200px);
            background: rgba(var(--yellow-light), 0.1);
            animation: float 15s ease-in-out infinite;
        }

        .persona-section::after {
            bottom: -80px;
            left: -80px;
            width: clamp(200px, 30vw, 300px);
            height: clamp(200px, 30vw, 300px);
            background: rgba(var(--yellow-dark), 0.05);
            animation: float 20s ease-in-out infinite reverse;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(10px, 15px) rotate(5deg);
            }

            50% {
                transform: translate(15px, 5px) rotate(0deg);
            }

            75% {
                transform: translate(5px, 10px) rotate(-5deg);
            }

            100% {
                transform: translate(0, 0) rotate(0deg);
            }
        }

        .persona-card {
            background: white;
            border-radius: var(--radius-md);
            padding: clamp(1.2rem, 3vw, 1.5rem);
            text-align: center;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            z-index: 1;
            will-change: transform, box-shadow;
        }

        .persona-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(var(--yellow-light), 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .persona-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)), rgb(var(--yellow-light)));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .persona-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .persona-card:hover::before {
            opacity: 1;
        }

        .persona-card:hover::after {
            transform: scaleX(1);
        }

        .persona-icon-container {
            position: relative;
            width: clamp(60px, 10vw, 80px);
            height: clamp(60px, 10vw, 80px);
            margin: 0 auto clamp(1rem, 2vw, 1.5rem);
        }

        .persona-icon-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgb(var(--gray-200));
            transform: scale(0.8);
            transition: all 0.5s ease;
        }

        .persona-card:hover .persona-icon-bg {
            transform: scale(1.1);
            background: rgb(var(--yellow-light), 0.2);
        }

        .persona-icon {
            position: relative;
            width: clamp(50px, 9vw, 70px);
            height: clamp(50px, 9vw, 70px);
            background: rgb(var(--gray-800));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: rgb(var(--yellow-primary));
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 2;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            will-change: transform, background, box-shadow;
        }

        .persona-card:hover .persona-icon {
            transform: rotate(10deg) scale(1.1);
            background: rgb(var(--black));
            box-shadow: 0 10px 20px rgba(var(--yellow-primary), 0.25);
        }

        .persona-title {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.1rem, 2.5vw, 1.3rem);
            font-weight: 600;
            margin-bottom: clamp(0.3rem, 1vw, 0.5rem);
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
        }

        .persona-title::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: rgb(var(--yellow-primary));
            transition: width 0.4s ease;
        }

        .persona-card:hover .persona-title::after {
            width: 100%;
        }

        .persona-subtitle {
            color: rgb(var(--gray-600));
            font-weight: 500;
            font-size: clamp(0.8rem, 1.8vw, 0.9rem);
            margin-bottom: clamp(0.5rem, 1.3vw, 0.75rem);
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .persona-card:hover .persona-subtitle {
            color: rgb(var(--yellow-dark));
            opacity: 1;
        }

        .persona-description {
            color: rgb(var(--gray-600));
            font-size: clamp(0.8rem, 1.8vw, 0.9rem);
            line-height: 1.6;
            transition: all 0.3s ease;
            max-height: none;
            overflow: hidden;
            opacity: 1;
            margin-bottom: 0;
            transform: none;
            display: block;
        }

        .persona-card:hover .persona-description {
            color: rgb(var(--gray-700));
        }

        /* Animated counter - always visible */
        .persona-counter {
            display: block;
            font-size: clamp(1.5rem, 4vw, 2.2rem);
            font-weight: 700;
            color: rgb(var(--yellow-primary));
            margin-bottom: clamp(0.3rem, 1vw, 0.5rem);
            opacity: 1;
            transform: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .persona-card:hover .persona-counter {
            color: rgb(var(--yellow-dark));
            transform: scale(1.1);
        }

        /* Sponsor Section */
        .sponsor-wrapper {
            position: relative;
            overflow: hidden;
            padding: clamp(30px, 6vw, 40px) 0;
            background: linear-gradient(90deg, rgb(var(--gray-100)) 0%, white 50%, rgb(var(--gray-100)) 100%);
            border-radius: var(--radius-lg);
            margin: clamp(15px, 3vw, 20px) 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .sponsor-wrapper::before,
        .sponsor-wrapper::after {
            content: '';
            position: absolute;
            top: 0;
            width: clamp(50px, 10vw, 100px);
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }

        .sponsor-wrapper::before {
            left: 0;
            background: linear-gradient(90deg, rgb(var(--gray-100)) 0%, rgba(var(--gray-100), 0) 100%);
        }

        .sponsor-wrapper::after {
            right: 0;
            background: linear-gradient(90deg, rgba(var(--gray-100), 0) 0%, rgb(var(--gray-100)) 100%);
        }

        .sponsor-scroll-container {
            overflow: hidden;
            position: relative;
            padding: 10px 0;
        }

        .sponsor-track {
            display: flex;
            position: relative;
            width: max-content;
            will-change: transform;
        }

        .sponsor-track {
            animation: slideSponsors var(--duration, 30s) linear infinite;
        }

        .sponsor-track:hover {
            animation-play-state: paused;
        }

        .sponsor-item {
            flex: 0 0 auto;
            padding: 0 clamp(20px, 5vw, 40px);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .sponsor-item:hover {
            transform: scale(1.1) translateY(-5px);
        }

        .sponsor-logo {
            height: clamp(40px, 10vw, 70px);
            max-width: clamp(100px, 20vw, 180px);
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.4s ease;
        }

        .sponsor-item:hover .sponsor-logo {
            filter: grayscale(0%);
            opacity: 1;
        }

        @keyframes slideSponsors {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* Closing Section yang Lebih Imersif */
        .closing-section {
            position: relative;
            min-height: clamp(400px, 70vh, 600px);
            background-color: #000;
            overflow: hidden;
            padding: 0;
        }

        .closing-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0.4;
            transition: transform 0.8s ease, opacity 0.8s ease;
            will-change: transform, opacity;
        }

        .closing-section:hover .closing-background {
            transform: scale(1.05);
            opacity: 0.5;
        }

        .closing-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.6) 100%);
            transition: background 0.5s ease;
        }

        .closing-section:hover .closing-overlay {
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.5) 100%);
        }

        .closing-content {
            position: relative;
            z-index: 2;
            padding: clamp(60px, 10vh, 100px) 0;
            color: white;
        }

        .closing-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(2rem, 5vw, 4rem);
            align-items: center;
        }

        .closing-text {
            padding-right: clamp(1rem, 3vw, 2rem);
        }

        .closing-heading {
            font-size: clamp(2rem, 6vw, 3.5rem);
            font-weight: 700;
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            line-height: 1.2;
            background: linear-gradient(45deg, rgb(var(--yellow-primary)), rgb(var(--yellow-light)));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            transition: all 0.4s ease;
        }

        .closing-section:hover .closing-heading {
            text-shadow: 0 0 10px rgba(var(--yellow-primary), 0.3);
        }

        .closing-description {
            font-size: clamp(1rem, 2.5vw, 1.2rem);
            line-height: 1.8;
            margin-bottom: clamp(1.5rem, 4vw, 2.5rem);
            color: rgba(255, 255, 255, 0.9);
        }

        .closing-cta {
            display: inline-flex;
            align-items: center;
            padding: clamp(0.8rem, 2vw, 1rem) clamp(1.5rem, 3vw, 2rem);
            background: rgb(var(--yellow-primary));
            color: black;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px rgba(var(--yellow-primary), 0.2);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .closing-cta::before {
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

        .closing-cta:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(var(--yellow-primary), 0.3);
            color: white;
        }

        .closing-cta:hover::before {
            transform: translateY(0);
        }

        .closing-cta i {
            transition: transform 0.3s ease;
            margin-left: 0.5rem;
        }

        .closing-cta:hover i {
            transform: translateX(5px);
        }

        .closing-image-container {
            position: relative;
            height: clamp(300px, 60vh, 500px);
        }

        .closing-person-image {
            position: absolute;
            height: 120%;
            width: auto;
            right: 0;
            bottom: 0;
            top: 3px;
            object-fit: cover;
            transform: translateY(20px);
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.3));
            will-change: transform;
        }

        .closing-section:hover .closing-person-image {
            transform: translateY(0) scale(1.05);
        }

        .closing-accent {
            position: absolute;
            width: clamp(150px, 20vw, 200px);
            height: clamp(150px, 20vw, 200px);
            border: 2px solid rgb(var(--yellow-primary));
            border-radius: 50%;
            opacity: 0.3;
            animation: pulse 3s infinite;
            will-change: transform, opacity;
        }

        .closing-accent:nth-child(1) {
            top: 20%;
            left: -100px;
        }

        .closing-accent:nth-child(2) {
            bottom: 10%;
            right: -50px;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.1;
            }

            100% {
                transform: scale(1);
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
            .kep-card:hover {
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

            .closing-person-image {
                left: 0;
                right: 0;
                margin: 0 auto;
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
            .kep-section {
                padding: clamp(40px, 8vw, 60px) 0;
            }

            .hero-banner::after {
                display: none;
            }

            .hero-banner {
                background-attachment: scroll;
            }

            .hero-text-container {
                padding: unset;
                text-align: center;
            }

            .hero-title {
                font-size: clamp(1.5rem, 7vw, 3rem);
                border-left: none;
                padding-left: 0;
                padding-bottom: clamp(0.5rem, 2vw, 1rem);
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

            .kep-features {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .kep-image {
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
            .kep-separator,
            .kep-content::before,
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
        .dark-mode-toggle {
            position: fixed;
            bottom: clamp(15px, 4vw, 30px);
            left: clamp(15px, 4vw, 30px);
            width: clamp(40px, 12vw, 50px);
            height: clamp(40px, 12vw, 50px);
            border-radius: var(--radius-full);
            background-color: rgb(var(--gray-800));
            color: rgb(var(--yellow-primary));
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0.7;
            transform: scale(0.9);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            will-change: transform, opacity;
        }

        .dark-mode-toggle:hover {
            opacity: 1;
            transform: scale(1);
            background-color: rgb(var(--black));
        }

        .dark-mode-toggle:active {
            transform: scale(0.9);
        }

        .dark-mode-toggle .fa-sun {
            display: none;
        }

        .dark-mode-toggle .fa-moon {
            display: block;
        }

        /* When in dark mode, swap icons */
        html.dark-mode .dark-mode-toggle .fa-sun {
            display: block;
        }

        html.dark-mode .dark-mode-toggle .fa-moon {
            display: none;
        }

        /* Dark mode styles (will override the media query) */
        html.dark-mode {
            color-scheme: dark;
        }

        html.dark-mode main {
            background-color: #000000;
        }

        html.dark-mode .kep-card,
        html.dark-mode .product-card,
        html.dark-mode .persona-card,
        html.dark-mode .feature-box,
        html.dark-mode .loading-screen {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }

        html.dark-mode .product-detail-btn {
            background: transparent;
            color: white;
        }

        html.dark-mode .kep-section,
        html.dark-mode .section-elegant,
        html.dark-mode .sponsor-wrapper {
            background-color: #000000;
            color: #e0e0e0;
        }

        html.dark-mode .sponsor-wrapper::before,
        html.dark-mode .sponsor-wrapper::after {
            background: none;
        }

        html.dark-mode .product-price {
            color: rgb(var(--yellow-light));
        }

        html.dark-mode .kep-description,
        html.dark-mode .feature-text,
        html.dark-mode .persona-description,
        html.dark-mode .persona-subtitle {
            color: rgba(255, 255, 255, 0.7);
        }

        html.dark-mode .section-title-elegant,
        html.dark-mode .kep-title,
        html.dark-mode .feature-title,
        html.dark-mode .persona-title {
            color: white;
        }

        html.dark-mode .product-description {
            color: white;
        }

        html.dark-mode .product-badge {
            background-color: white;
            color: black;
        }

        html.persona-section {
            overflow: visible;

        }

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
    <div id="loading-screen" class="loading-screen">
        <div class="logo-container">
            <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }} Logo" class="company-logo">
        </div>
        <div class="loading-bar">
            <div class="loading-progress"></div>
        </div>
    </div>

    <button id="scrollToTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas halaman">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Add the dark mode toggle button here -->
    <button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle Dark Mode">
        <i class="fas fa-moon"></i>
        <i class="fas fa-sun"></i>
    </button>

    <!-- Carousel -->
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="false"
        data-aos="fade-in">
        <div class="carousel-indicators">
            @foreach ($carousel_items as $index => $item)
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}"
                    class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach ($carousel_items as $item)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <img src="{{ Storage::url($item->image) }}" class="d-block w-100" alt="{{ $item->title }}"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                    <div class="carousel-caption">
                        <h2>{{ $item->title }}</h2>
                        <p>{{ $item->subtitle }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev"
            aria-label="Slide sebelumnya">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next"
            aria-label="Slide berikutnya">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <!-- Separator -->
    <div class="light-separator" data-aos="fade-in" aria-hidden="true">
        <div class="light-beam light-beam-left"></div>
        <div class="light-beam light-beam-right"></div>
    </div>

    <!-- KEP Description Section -->
    <section class="kep-section" data-aos="fade-up" id="about-section">
        <div class="container">
            <h2 class="kep-title" data-aos="fade-down">Tentang Koperasi Energi dan Pertambangan</h2>
            <div class="kep-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="kep-card" data-aos="fade-up" data-aos-delay="300">
                <div class="kep-image" data-aos="zoom-in" data-aos-delay="400">
                    <img src="{{ Storage::url($company->image) }}" alt="KEP Headquarters" loading="lazy">
                </div>
                <div class="kep-content">
                    <p class="kep-description" data-aos="fade-up" data-aos-delay="500">
                        Koperasi Energi dan Pertambangan adalah lembaga yang berfokus pada pengembangan dan pemberdayaan
                        sektor energi dan pertambangan.
                        Kami berkomitmen untuk meningkatkan daya saing dan ketahanan energi melalui sinergi antara koperasi
                        dan masyarakat.
                    </p>
                    <div class="kep-features">
                        @foreach ($features->where('is_active', true)->sortBy('order') as $feature)
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class="{{ $feature->icon }} fa-2x text-dark"></i>
                                </div>
                                <h4 class="feature-title">{{ $feature->title }}</h4>
                                <p class="feature-text">{{ $feature->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Welcome Section -->
    <section class="section-elegant" data-aos="fade-up" id="features-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Mengapa Memilih Kami?</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="row g-4">
                @foreach ($features as $feature)
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="feature-box">
                            <div class="feature-icon">
                                <i class="{{ $feature->icon }} fa-2x text-dark"></i>
                            </div>
                            <h4 class="feature-title">{{ $feature->title }}</h4>
                            <p class="feature-text">{{ $feature->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- View More Section -->
    <section class="hero-banner" data-aos="fade-up" id="hero-section"
        style="background-image: url('{{ Storage::url($company->image) }}')">
        <div class="hero-content">
            <div class="container-fluid p-0">
                <div class="row m-0">
                    <div class="col-lg-6 col-md-12 p-0" data-aos="fade-right" data-aos-delay="200">
                        <div class="hero-text-container">
                            <h1 class="hero-title">Tentang Kami</h1>
                            <p class="hero-description">
                                Kami berfokus pada pengelolaan sumber daya energi dan pertambangan untuk mendukung
                                kesejahteraan masyarakat.
                            </p>
                            <a href="{{ route('about') }}" class="btn-view-more">LIHAT SELENGKAPNYA</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 p-0" data-aos="fade-left" data-aos-delay="300">
                        <div class="hero-action-container">
                            <a href="{{ route('products.index') }}" class="product-link">
                                atau Mulai Cari Produk
                                <div class="animated-arrows">
                                    <span class="arrow arrow-first" aria-hidden="true"></span>
                                    <span class="arrow arrow-second" aria-hidden="true"></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Best Products Section -->
    <section class="section-elegant" data-aos="fade-up" id="products-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Produk Unggulan Kami</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="row g-4">
                @foreach ($best_products as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up"
                        data-aos-delay="{{ $loop->index * 200 }}">
                        <div class="product-card">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="product-image"
                                    alt="{{ $product->name }}" loading="lazy">
                            @endif
                            <div class="product-content">
                                <h5 class="mb-3">{{ $product->name }}</h5>
                                <p class="product-description">{{ $product->short_description }}</p>
                                <div class="product-footer">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="product-price">{{ $product->formatted_price }}</span>
                                        <span class="product-badge">{{ $product->sold_count }}+ Terjual</span>
                                    </div>
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-detail-btn">
                                        Lihat Detail
                                    </a>
                                </div>
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
    <section class="persona-section" data-aos="fade-up" id="persona-section">
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

    <!-- Sponsor Section -->
    <section class="section-elegant" data-aos="fade-up" id="sponsors-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Partner & Sponsor Kami</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="sponsor-wrapper" data-aos="fade-up" data-aos-delay="300">
                <div class="sponsor-scroll-container">
                    <div class="sponsor-track" id="sponsorTrack">
                        <!-- Original sponsors -->
                        @foreach ($sponsors as $sponsor)
                            <div class="sponsor-item">
                                <a href="{{ $sponsor->website_url }}" target="_blank" rel="noopener noreferrer"
                                    aria-label="{{ $sponsor->name }}">
                                    <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}"
                                        class="sponsor-logo" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                        <!-- Duplicate sponsors for seamless loop -->
                        @foreach ($sponsors as $sponsor)
                            <div class="sponsor-item">
                                <a href="{{ $sponsor->website_url }}" target="_blank" rel="noopener noreferrer"
                                    aria-label="{{ $sponsor->name }}">
                                    <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}"
                                        class="sponsor-logo" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Closing Section -->
    <section class="closing-section" data-aos="fade-up" id="contact-section">
        <div class="closing-background" style="background-image: url('{{ asset('images/construction-2.jpg') }}')">
        </div>
        <div class="closing-overlay"></div>
        <div class="container closing-content">
            <div class="closing-grid">
                <div class="closing-text" data-aos="fade-right" data-aos-delay="200">
                    <h2 class="closing-heading">Mari Bergabung dengan Kami</h2>
                    <p class="closing-description">
                        Bergabunglah dalam revolusi energi dan pertambangan yang lebih berkelanjutan bersama kami.
                    </p>
                    <a href="{{ route('about') }}#company-location" class="closing-cta">
                        Hubungi Kami
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="closing-image-container" data-aos="fade-left" data-aos-delay="300">
                    <img src="{{ asset('images/engineer.png') }}" alt="Professional Engineer"
                        class="closing-person-image" loading="lazy">
                </div>
            </div>
        </div>
        <div class="closing-accent" aria-hidden="true"></div>
        <div class="closing-accent" aria-hidden="true"></div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfigurasi loading screen yang lebih efisien
            const loadingScreen = document.getElementById('loading-screen');
            const startTime = new Date().getTime();
            const minLoadTime = 800; // Waktu minimum loading dalam milliseconds

            // Fungsi untuk menghilangkan loading screen
            const hideLoadingScreen = () => {
                const currentTime = new Date().getTime();
                const elapsedTime = currentTime - startTime;

                // Pastikan waktu minimum terpenuhi
                if (elapsedTime >= minLoadTime) {
                    loadingScreen.classList.add('fade-out');
                    setTimeout(() => {
                        loadingScreen.remove();
                    }, 300);
                } else {
                    // Tunggu hingga waktu minimum tercapai
                    setTimeout(hideLoadingScreen, minLoadTime - elapsedTime);
                }
            };

            // Aktifkan setelah semua konten dimuat
            window.addEventListener('load', hideLoadingScreen);

            // Fallback jika terjadi masalah loading
            setTimeout(hideLoadingScreen, 3000);

            // Tombol scroll to top yang diperbarui
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');

            // Tampilkan/sembunyikan tombol berdasarkan posisi scroll
            const toggleScrollBtn = () => {
                if (window.pageYOffset > 400) {
                    scrollToTopBtn.classList.add('visible');
                } else {
                    scrollToTopBtn.classList.remove('visible');
                }
            };

            window.addEventListener('scroll', toggleScrollBtn);

            // Smooth scroll ke atas ketika tombol diklik
            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            const darkModeToggle = document.getElementById('darkModeToggle');
            const htmlElement = document.documentElement;

            // Check for saved preference or system preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' ||
                (savedTheme === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                htmlElement.classList.add('dark-mode');
            }

            // Update button appearance based on current mode
            updateButtonAppearance();

            // Toggle dark mode on button click
            darkModeToggle.addEventListener('click', function() {
                htmlElement.classList.toggle('dark-mode');

                // Save preference to localStorage
                if (htmlElement.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }

                // Update button appearance
                updateButtonAppearance();
            });

            // Function to update button appearance
            function updateButtonAppearance() {
                if (htmlElement.classList.contains('dark-mode')) {
                    darkModeToggle.setAttribute('aria-label', 'Switch to Light Mode');
                    darkModeToggle.style.backgroundColor = 'rgb(var(--yellow-primary))';
                    darkModeToggle.style.color = 'rgb(var(--black))';
                } else {
                    darkModeToggle.setAttribute('aria-label', 'Switch to Dark Mode');
                    darkModeToggle.style.backgroundColor = 'rgb(var(--gray-800))';
                    darkModeToggle.style.color = 'rgb(var(--yellow-primary))';
                }
            }

            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                // Only change if user hasn't manually set a preference
                if (!localStorage.getItem('theme')) {
                    if (e.matches) {
                        htmlElement.classList.add('dark-mode');
                    } else {
                        htmlElement.classList.remove('dark-mode');
                    }
                    updateButtonAppearance();
                }
            });

            // Perbaikan efek parallax - metode yang lebih reliable
            const heroSection = document.querySelector('.hero-banner');

            if (heroSection) {
                // Pastikan gambar latar belakang sudah dimuat sebelum menerapkan efek
                const bgUrl = window.getComputedStyle(heroSection).backgroundImage;

                if (bgUrl && bgUrl !== 'none') {
                    // Simpan posisi background awal
                    const initialBgPosition = window.getComputedStyle(heroSection).backgroundPosition;

                    // Fungsi parallax yang diperbarui
                    function parallaxEffect() {
                        // Dapatkan posisi scroll dan posisi elemen
                        const scrollPosition = window.pageYOffset;
                        const heroRect = heroSection.getBoundingClientRect();

                        // Periksa apakah hero section dalam viewport
                        const isInView = (
                            heroRect.bottom > 0 &&
                            heroRect.top < window.innerHeight
                        );

                        if (isInView) {
                            // Hitung efek parallax berdasarkan seberapa jauh kita scroll
                            // Tingkatkan multiplier untuk efek yang lebih terlihat (dari 0.3 ke 0.5)
                            const parallaxOffset = scrollPosition * 0.5;

                            // Terapkan efek parallax ke background position
                            heroSection.style.backgroundPosition = `center calc(50% + ${parallaxOffset}px)`;

                            // Tambahkan debug info jika diperlukan (hapus pada produksi)
                            // console.log(`Parallax applied: ${parallaxOffset}px`);
                        }
                    }

                    // Jalankan sekali saat halaman dimuat
                    parallaxEffect();

                    // Tambahkan event listener dengan throttling untuk performa
                    let isScrolling = false;
                    window.addEventListener('scroll', function() {
                        if (!isScrolling) {
                            window.requestAnimationFrame(function() {
                                parallaxEffect();
                                isScrolling = false;
                            });
                            isScrolling = true;
                        }
                    });

                    // Tambahkan handler untuk resize window
                    window.addEventListener('resize', parallaxEffect);

                    // Tambahkan debug message di console
                    console.log('Parallax effect initialized for hero banner');
                } else {
                    console.warn('Hero banner tidak memiliki background image');
                }
            } else {
                console.warn('Hero banner element tidak ditemukan');
            }

            // Inisialisasi animasi sponsor
            const initSponsors = () => {
                const track = document.getElementById('sponsorTrack');
                if (!track) return;

                const items = track.querySelectorAll('.sponsor-item');
                if (items.length === 0) return;

                // Hitung lebar total dan sesuaikan durasi animasi
                let totalWidth = 0;
                const firstHalfItems = Array.from(items).slice(0, items.length / 2);

                firstHalfItems.forEach(item => {
                    totalWidth += item.offsetWidth;
                });

                const duration = Math.max(20, totalWidth / 50);
                track.style.setProperty('--duration', `${duration}s`);
            };

            // Inisialisasi animasi counter
            const initCounters = () => {
                const counterElements = document.querySelectorAll('.persona-counter');

                // Fungsi untuk menganimasikan counter saat dalam viewport
                const animateCounters = () => {
                    counterElements.forEach(counter => {
                        if (isInViewport(counter)) {
                            const target = parseInt(counter.getAttribute('data-count'));
                            const current = parseInt(counter.textContent);

                            if (current < target) {
                                // Gunakan proporsi untuk penambahan yang lebih smooth
                                const remaining = target - current;
                                const increment = Math.max(1, Math.ceil(remaining / 15));
                                const newValue = Math.min(current + increment, target);

                                counter.textContent = newValue + '+';

                                if (newValue < target) {
                                    // Terapkan penundaan berdasarkan seberapa dekat ke target
                                    const delay = 1000 / (target / 10);
                                    setTimeout(() => requestAnimationFrame(animateCounters), delay);
                                }
                            }
                        }
                    });
                };

                // Observer API untuk mendeteksi elemen dalam viewport
                const observerOptions = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 0.1
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounters();
                        }
                    });
                }, observerOptions);

                counterElements.forEach(counter => {
                    observer.observe(counter);
                });
            };

            // Fungsi utility untuk mendeteksi elemen dalam viewport
            function isInViewport(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.bottom >= 0 &&
                    rect.left <= (window.innerWidth || document.documentElement.clientWidth) &&
                    rect.right >= 0
                );
            }

            // Inisialisasi efek magnetic untuk kartu
            const initMagneticEffect = () => {
                // Hanya terapkan pada perangkat non-touch
                if (window.matchMedia('(hover: hover)').matches) {
                    const personaCards = document.querySelectorAll('.persona-card');

                    personaCards.forEach(card => {
                        // Mouse move pada kartu
                        card.addEventListener('mousemove', function(e) {
                            const cardRect = card.getBoundingClientRect();
                            const cardCenterX = cardRect.left + cardRect.width / 2;
                            const cardCenterY = cardRect.top + cardRect.height / 2;

                            const mouseX = e.clientX;
                            const mouseY = e.clientY;

                            // Hitung jarak dari pusat (sebagai persentase)
                            const distanceX = (mouseX - cardCenterX) / (cardRect.width / 2);
                            const distanceY = (mouseY - cardCenterY) / (cardRect.height / 2);

                            // Batasi efek kemiringan
                            const tiltLimitX = 5; // derajat
                            const tiltLimitY = 5; // derajat

                            const tiltX = -1 * distanceY * tiltLimitY;
                            const tiltY = distanceX * tiltLimitX;

                            // Terapkan efek kemiringan dengan transisi yang lebih halus
                            card.style.transition = 'transform 0.1s ease-out';
                            card.style.transform =
                                `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateY(-10px) scale(1.02)`;

                            // Gerakkan ikon mengikuti mouse
                            const icon = card.querySelector('.persona-icon');
                            if (icon) {
                                icon.style.transition = 'transform 0.1s ease-out';
                                icon.style.transform =
                                    `translate(${distanceX * 5}px, ${distanceY * 5}px) rotate(10deg) scale(1.1)`;
                            }
                        });

                        // Reset posisi card saat mouse leave
                        card.addEventListener('mouseleave', function() {
                            // Reset posisi kartu dengan halus
                            card.style.transition = 'transform 0.5s ease-out';
                            card.style.transform = '';

                            // Reset posisi ikon
                            const icon = card.querySelector('.persona-icon');
                            if (icon) {
                                icon.style.transition = 'transform 0.5s ease-out';
                                icon.style.transform = '';
                            }
                        });
                    });
                }
            };

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
