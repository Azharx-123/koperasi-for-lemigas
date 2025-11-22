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
        }

        /* Product Detail Hero Section */
        .product-hero {
            padding: 3rem 0 1rem;
            background: linear-gradient(135deg, rgb(var(--gray-100)) 0%, white 100%);
            position: relative;
            overflow: hidden;
        }

        .product-hero::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(var(--yellow-primary), 0.1);
            z-index: 0;
        }

        .product-hero::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(var(--yellow-primary), 0.05);
            z-index: 0;
        }

        /* Product Image */
        .product-image-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            z-index: 1;
            background: white;
        }

        .product-main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .product-image-container:hover .product-main-image {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 8px 16px;
            background: rgba(var(--yellow-primary), 0.9);
            color: rgb(var(--black));
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 30px;
            z-index: 2;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Product Info */
        .product-info-container {
            position: relative;
            z-index: 1;
            padding: 2rem 0 2rem 3rem;
        }

        .product-category {
            display: inline-flex;
            align-items: center;
            background: rgba(var(--gray-200), 0.7);
            color: rgb(var(--gray-800));
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(5px);
        }

        .product-category i {
            margin-right: 8px;
            color: rgb(var(--yellow-dark));
        }

        .product-title {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: rgb(var(--black));
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .product-meta {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            gap: 2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            color: rgb(var(--gray-600));
            font-size: 0.95rem;
        }

        .meta-item i {
            margin-right: 8px;
            color: rgb(var(--yellow-dark));
            font-size: 1.1rem;
        }

        .product-price-container {
            margin-bottom: 2rem;
        }

        .price-label {
            font-size: 1rem;
            color: rgb(var(--gray-600));
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: rgb(var(--black));
            display: flex;
            align-items: center;
        }

        .price-currency {
            font-size: 1.5rem;
            margin-right: 5px;
            color: rgb(var(--yellow-dark));
        }

        .stock-info {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            margin-bottom: 2rem;
            border-top: 1px solid rgba(var(--gray-300), 0.7);
            border-bottom: 1px solid rgba(var(--gray-300), 0.7);
        }

        .stock-label {
            display: flex;
            align-items: center;
            font-weight: 500;
            margin-right: 1rem;
        }

        .stock-label i {
            margin-right: 8px;
            color: rgb(var(--yellow-dark));
        }

        .stock-value {
            background: rgba(var(--gray-200), 0.7);
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .stock-value.in-stock {
            color: #28a745;
        }

        .stock-value.low-stock {
            color: #ffc107;
        }

        .stock-value.out-of-stock {
            color: #dc3545;
        }

        /* Add to Cart Form */
        .add-to-cart-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quantity-input-group {
            position: relative;
            width: 150px;
            height: 50px;
            display: flex;
            align-items: center;
            border: 2px solid rgba(var(--gray-300), 1);
            border-radius: 50px;
            overflow: hidden;
            background: white;
        }

        .quantity-btn {
            width: 40px;
            height: 100%;
            border: none;
            background: transparent;
            color: rgb(var(--gray-600));
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:hover {
            color: rgb(var(--yellow-dark));
            background: rgba(var(--gray-100), 0.5);
        }

        .quantity-input {
            flex: 1;
            height: 100%;
            text-align: center;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: rgb(var(--black));
            outline: none;
            -moz-appearance: textfield;
        }

        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .add-to-cart-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 2rem;
            height: 50px;
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(var(--yellow-primary), 0.2);
        }

        .add-to-cart-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(var(--yellow-primary), 0.3);
        }

        .add-to-cart-btn i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Product Details Section */
        .product-details-section {
            padding: 2rem 0;
            position: relative;
            background: white;
        }

        .details-separator {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgb(var(--yellow-primary)) 50%, transparent 100%);
            width: 150px;
            margin: 2rem auto;
        }

        .product-description-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .description-header {
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            padding: 1.5rem 2rem;
            position: relative;
            z-index: 1;
        }

        .description-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .description-body {
            padding: 2.5rem;
            color: rgb(var(--gray-800));
            line-height: 1.8;
        }

        .description-body p,
        .description-body ul,
        .description-body ol {
            margin-bottom: 1.5rem;
        }

        .description-body strong {
            color: rgb(var(--black));
        }

        /* Related Products Section */
        .related-products-section {
            padding: 5rem 0;
            background: rgb(var(--gray-100));
            position: relative;
            overflow: hidden;
        }

        .related-products-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(var(--yellow-primary), 0.05);
            z-index: 0;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: rgb(var(--black));
            text-align: center;
            margin-bottom: 1rem;
        }

        .related-product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
        }

        .related-product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .related-product-image {
            height: 220px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .related-product-card:hover .related-product-image {
            transform: scale(1.05);
        }

        .related-product-body {
            padding: 1.5rem;
            position: relative;
        }

        .related-product-category {
            font-size: 0.85rem;
            color: rgb(var(--gray-600));
            margin-bottom: 0.7rem;
        }

        .related-product-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.4;
            color: rgb(var(--black));
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.8rem;
        }

        .related-product-price {
            color: rgb(var(--yellow-dark));
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .related-product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .related-product-rating {
            display: flex;
            align-items: center;
            color: rgb(var(--gray-600));
            font-size: 0.9rem;
        }

        .related-product-rating i {
            color: #ffc107;
            margin-right: 5px;
        }

        .related-product-sold {
            font-size: 0.9rem;
            background: rgb(var(--gray-800));
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .view-detail-btn {
            display: block;
            text-align: center;
            padding: 0.8rem 0;
            background: transparent;
            color: rgb(var(--black));
            border: 2px solid rgb(var(--gray-300));
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .view-detail-btn:hover {
            background: rgb(var(--yellow-primary));
            border-color: rgb(var(--yellow-primary));
            color: rgb(var(--black));
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(var(--yellow-primary), 0.2);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .product-info-container {
                padding: 2rem 0 0 0;
            }

            .product-title {
                font-size: 2rem;
            }

            .product-price {
                font-size: 2rem;
            }

            .product-hero {
                padding: 4rem 0 3rem;
            }
        }

        @media (max-width: 768px) {
            .product-main-image {
                height: 350px;
            }

            .add-to-cart-form {
                flex-direction: column;
                gap: 1rem;
            }

            .quantity-input-group {
                width: 100%;
            }

            .section-title {
                font-size: 2rem;
            }

            .product-details-section,
            .related-products-section {
                padding: 3rem 0;
            }
        }

        /* Notifikasi Keranjang */
        .cart-notification {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 1000;
            max-width: 400px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-left: 4px solid rgb(var(--yellow-primary));
            overflow: hidden;
            transform: translateX(120%);
            transition: transform 0.3s ease;
        }

        .cart-notification.show {
            transform: translateX(0);
        }

        .cart-notification-content {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .cart-notification-content i {
            font-size: 2rem;
            color: #28a745;
            margin-bottom: 15px;
        }

        .notification-text {
            margin-bottom: 15px;
        }

        .notification-text h4 {
            font-weight: 600;
            margin-bottom: 5px;
            color: rgb(var(--black));
        }

        .notification-text p {
            color: rgb(var(--gray-600));
            font-size: 0.9rem;
            margin: 0;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .view-cart-btn {
            padding: 8px 16px;
            background: rgb(var(--yellow-primary));
            color: rgb(var(--black));
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }

        .view-cart-btn:hover {
            background: rgb(var(--yellow-dark));
            color: white;
        }

        .continue-shopping-btn {
            padding: 8px 16px;
            background: white;
            color: rgb(var(--gray-800));
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgb(var(--gray-300));
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .continue-shopping-btn:hover {
            background: rgb(var(--gray-200));
        }

        .close-notification {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            font-size: 1.2rem;
            color: rgb(var(--gray-600));
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-notification:hover {
            color: rgb(var(--black));
        }

        @media (max-width: 576px) {
            .cart-notification {
                top: auto;
                right: 0;
                bottom: 0;
                left: 0;
                max-width: 100%;
                border-radius: 10px 10px 0 0;
                border-left: none;
                border-top: 4px solid rgb(var(--yellow-primary));
            }

            .notification-actions {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Product Hero Section -->
    <section class="product-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="product-image-container">
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-main-image">
                        @if ($product->is_new)
                            <div class="product-badge">New</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="product-info-container">
                        <div class="product-category">
                            <i class="fas fa-tag me-2"></i>
                            {{ $product->category->name }}
                        </div>
                        <h1 class="product-title">{{ $product->name }}</h1>

                        <div class="product-meta">
                            <div class="meta-item">
                                <i class="fas fa-star me-2"></i>
                                <span>{{ number_format($product->rating_average, 1) }} Rating</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span>{{ $product->sold_count }}+ Terjual</span>
                            </div>
                        </div>

                        <div class="product-price-container">
                            <div class="price-label">Harga</div>
                            <div class="product-price">
                                <span class="price-currency">Rp</span>
                                {{ substr($product->formatted_price, 3) }}
                            </div>
                        </div>

                        <div class="stock-info">
                            <div class="stock-label">
                                <i class="fas fa-box me-2"></i>
                                Ketersediaan:
                            </div>

                            @if ($product->stock > 10)
                                <div class="stock-value in-stock">Tersedia ({{ $product->stock }})</div>
                            @elseif($product->stock > 0)
                                <div class="stock-value low-stock">Stok Terbatas ({{ $product->stock }})</div>
                            @else
                                <div class="stock-value out-of-stock">Stok Habis</div>
                            @endif
                        </div>

                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                            @csrf
                            <div class="quantity-input-group">
                                <button type="button" class="quantity-btn decrease-btn">-</button>
                                <input type="number" name="quantity" class="quantity-input" value="1" min="1"
                                    max="{{ $product->stock }}">
                                <button type="button" class="quantity-btn increase-btn">+</button>
                            </div>

                            <button type="submit" class="add-to-cart-btn">
                                <i class="fas fa-cart-plus me-2"></i>
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notifikasi Tambah Keranjang -->
    <div id="cart-notification" class="cart-notification" style="display: none;">
        <div class="cart-notification-content">
            <i class="fas fa-check-circle fs-3 me-2 mb-2"></i>
            <div class="notification-text">
                <h4>Produk berhasil ditambahkan ke keranjang!</h4>
                <p>Silakan periksa keranjang belanja Anda untuk melanjutkan checkout.</p>
            </div>
            <div class="notification-actions">
                <a href="{{ route('cart.show') }}" class="view-cart-btn">Lihat Keranjang</a>
                <button class="continue-shopping-btn" onclick="closeNotification()">Lanjut Belanja</button>
            </div>
            <button class="close-notification" onclick="closeNotification()">&times;</button>
        </div>
    </div>

    <!-- Product Details Section -->
    <section class="product-details-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Deskripsi Produk</h2>
            <div class="details-separator" data-aos="zoom-in" data-aos-delay="200"></div>

            <div class="product-description-card" data-aos="fade-up" data-aos-delay="300">
                <div class="description-header">
                    <h3>Detail Produk</h3>
                </div>
                <div class="description-body">
                    {!! $product->description !!}
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products Section -->
    @if ($relatedProducts->count() > 0)
        <section class="related-products-section">
            <div class="container">
                <h2 class="section-title" data-aos="fade-up">Produk Terkait</h2>
                <div class="details-separator" data-aos="zoom-in" data-aos-delay="200"></div>

                <div class="row g-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ 300 + $loop->index * 100 }}">
                            <div class="related-product-card">
                                <img src="{{ Storage::url($relatedProduct->image) }}" alt="{{ $relatedProduct->name }}"
                                    class="related-product-image">

                                <div class="related-product-body">
                                    <div class="related-product-category">{{ $relatedProduct->category->name }}</div>
                                    <h3 class="related-product-title">{{ $relatedProduct->name }}</h3>

                                    <div class="related-product-meta">
                                        <div class="related-product-rating">
                                            <i class="fas fa-star me-2"></i>
                                            {{ number_format($relatedProduct->rating_average, 1) }}
                                        </div>
                                        <div class="related-product-sold">{{ $relatedProduct->sold_count }}+ Terjual</div>
                                    </div>

                                    <div class="related-product-price">{{ $relatedProduct->formatted_price }}</div>

                                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="view-detail-btn">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.querySelector('.quantity-input');
            const decreaseBtn = document.querySelector('.decrease-btn');
            const increaseBtn = document.querySelector('.increase-btn');
            const maxStock = {{ $product->stock }};

            decreaseBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });

            increaseBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value < maxStock) {
                    quantityInput.value = value + 1;
                }
            });

            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value);
                if (isNaN(value) || value < 1) {
                    this.value = 1;
                } else if (value > maxStock) {
                    this.value = maxStock;
                }
            });

            // Script baru untuk handling form dan notifikasi
            const addToCartForm = document.querySelector('.add-to-cart-form');
            const cartNotification = document.getElementById('cart-notification');

            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const url = this.getAttribute('action');

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Show notification
                            showNotification();

                            // Update cart count in header if you have one
                            if (data.cartCount) {
                                const cartCountElement = document.querySelector('.cart-count');
                                if (cartCountElement) {
                                    cartCountElement.textContent = data.cartCount;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal menambahkan produk ke keranjang. Silakan coba lagi.');
                        });
                });
            }
        });

        function showNotification() {
            const notification = document.getElementById('cart-notification');
            notification.style.display = 'block';

            // Add the show class after a small delay to trigger the animation
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            // Automatically hide after 5 seconds
            setTimeout(() => {
                closeNotification();
            }, 5000);
        }

        function closeNotification() {
            const notification = document.getElementById('cart-notification');
            notification.classList.remove('show');

            // Hide the element after the animation completes
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }
    </script>

    <script type="application/ld+json">
    {
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "image": "{{ Storage::url($product->image) }}",
    "description": "{{ $product->short_description }}",
    "brand": {
    "@type": "Brand",
    "name": "{{ config('app.name') }}"
    },
    "offers": {
    "@type": "Offer",
    "url": "{{ route('products.show', $product->slug) }}",
    "priceCurrency": "IDR",
    "price": "{{ $product->price }}",
    "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
    },
    "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ $product->rating_average }}",
    "reviewCount": "{{ $product->rating_count }}"
    }
    }
</script>
@endpush
