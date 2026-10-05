@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/products-show.css')
    @vite('resources/css/pages/ratings-index.css')
@endpush

@section('title', $product->name)
@section('meta_description', \Illuminate\Support\Str::limit($product->short_description ?? $product->name, 160))
@section('meta_image', \App\Helpers\ImageHelper::url($product->image))

@section('content')
    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Product Hero Section -->
    <section class="product-hero">
        <div class="container">
            <nav class="product-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('welcome') }}">Beranda</a>
                <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                <a href="{{ route('products.index') }}">Produk</a>
                <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                <a href="{{ route('products.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
                <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
                <span class="breadcrumb-current">{{ $product->name }}</span>
            </nav>
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="product-image-container">
                        <img src="{{ \App\Helpers\ImageHelper::url($product->image) }}" alt="{{ $product->name }}" class="product-main-image">
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
                            <a href="#rating-section" class="meta-item meta-item-link">
                                <i class="fas fa-star me-2"></i>
                                <span>{{ number_format($product->rating_average, 1) }} Rating ({{ $product->rating_count }} ulasan)</span>
                            </a>
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
                                <button type="button" class="quantity-btn decrease-btn" @if ($product->stock <= 0) disabled @endif>-</button>
                                <input type="number" name="quantity" class="quantity-input" value="1" min="1"
                                    max="{{ $product->stock }}" @if ($product->stock <= 0) disabled @endif>
                                <button type="button" class="quantity-btn increase-btn" @if ($product->stock <= 0) disabled @endif>+</button>
                            </div>

                            <button type="submit" class="add-to-cart-btn" @if ($product->stock <= 0) disabled @endif>
                                <i class="fas fa-cart-plus me-2"></i>
                                {{ $product->stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
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
            <i id="cart-notification-icon" class="fas fa-check-circle fs-3 me-2 mb-2"></i>
            <div class="notification-text">
                <h4 id="cart-notification-title">Produk berhasil ditambahkan ke keranjang!</h4>
                <p id="cart-notification-message">Silakan periksa keranjang belanja Anda untuk melanjutkan checkout.</p>
            </div>
            <div id="cart-notification-actions" class="notification-actions">
                <a href="{{ route('cart.show') }}" class="view-cart-btn">Lihat Keranjang</a>
               <a href="{{ route('products.index') }}" class="continue-shopping-btn">Lanjut Belanja</a>    </div>
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
                    {!! str($product->description)->sanitizeHtml() !!}
                </div>
            </div>
        </div>
    </section>

    <!-- Rating & Ulasan Section -->
    <section class="rating-section" id="rating-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Rating & Ulasan</h2>
            <div class="details-separator" data-aos="zoom-in" data-aos-delay="200"></div>

            <div class="rating-section-grid" data-aos="fade-up" data-aos-delay="300">
                <div class="rating-summary-col">
                    <x-rating-display :product="$product" />
                </div>

                <div class="rating-form-col">
                    @auth
                        @if ($userRating && !$userRating->is_approved)
                            <div class="alert alert-info">
                                <i class="fas fa-clock me-2"></i>
                                Ulasan Anda sedang menunggu persetujuan admin sebelum tampil untuk pembeli lain. Anda tetap
                                bisa mengubahnya kapan saja selama menunggu.
                            </div>
                        @endif
                        <x-rating-form :product="$product" :userRating="$userRating" />
                    @else
                        <div class="login-to-rate-card">
                            <i class="fas fa-lock login-to-rate-icon"></i>
                            <p>Masuk untuk memberi rating dan ulasan pada produk ini.</p>
                            <a href="{{ route('login') }}" class="btn btn-dark">Masuk untuk Memberi Rating</a>
                        </div>
                    @endauth
                </div>
            </div>

            @if ($previewRatings->count() > 0)
                <div class="rating-preview-list" data-aos="fade-up" data-aos-delay="400">
                    @foreach ($previewRatings as $rating)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="reviewer-name">{{ $rating->user?->name ?? 'Pengguna' }}</div>
                                        <div class="review-date">{{ $rating->created_at->diffForHumans() }}</div>
                                        @if ($rating->verified_purchase)
                                            <div class="verified-badge mt-1">
                                                <i class="fas fa-check-circle"></i> Pembelian Terverifikasi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="review-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa{{ $i <= $rating->score ? 's' : 'r' }} fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                            @if ($rating->review)
                                <div class="review-content">{{ Illuminate\Support\Str::limit($rating->review, 200) }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-4" data-aos="fade-up">
                <a href="{{ route('ratings.index', $product) }}" class="btn btn-outline-dark">
                    Lihat Semua Ulasan ({{ $product->rating_count }}) <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Related Products Section -->
    @if ($relatedProducts->count() > 0)
        <section class="related-products-section">
            <div class="container">
                <h2 class="section-title" data-aos="fade-up">Produk Terkait</h2>
                <div class="details-separator" data-aos="zoom-in" data-aos-delay="200"></div>

                <div class="product-grid">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="product-card" data-aos="fade-up" data-aos-delay="{{ 300 + $loop->index * 100 }}">
                            <div class="product-image-container">
                                <img src="{{ \App\Helpers\ImageHelper::url($relatedProduct->image) }}" alt="{{ $relatedProduct->name }}"
                                    class="product-image">
                            </div>
                            <div class="product-content">
                                <div class="product-info">
                                    <div class="product-category">{{ $relatedProduct->category->name }}</div>
                                    <h3 class="product-title">{{ $relatedProduct->name }}</h3>
                                </div>
                                <div class="product-data">
                                    <div class="product-meta">
                                        <span class="badge-meta badge-rating">
                                            <i class="fas fa-star"></i>
                                            {{ number_format($relatedProduct->rating_average, 1) }}
                                            ({{ $relatedProduct->rating_count }})
                                        </span>
                                        <span class="badge-meta badge-sales">
                                            <i class="fas fa-shopping-cart"></i>
                                            {{ $relatedProduct->sold_count }}+
                                        </span>
                                    </div>
                                    <div class="product-price">{{ $relatedProduct->formatted_price }}</div>
                                    <a href="{{ route('products.show', $relatedProduct->slug) }}"
                                        class="btn btn-outline-dark w-100 btn-detail">
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
                        .then(response => response.json().then(data => ({ ok: response.ok, data })))
                        .then(({ ok, data }) => {
                            // The backend responds 422 with either {success:false,
                            // message} (business-rule failures like "out of stock")
                            // or, for validation errors, {message, errors} with no
                            // "success" key at all — so !data.success covers both.
                            if (!ok || !data.success) {
                                showNotification(false, data.message || 'Gagal menambahkan produk ke keranjang.');
                                return;
                            }

                            showNotification(true, data.message || 'Produk berhasil ditambahkan ke keranjang!');

                            // Update cart count in header if you have one
                            if (data.cartCount) {
                                const cartCountElement = document.querySelector('.cart-count');
                                if (cartCountElement) {
                                    cartCountElement.textContent = data.cartCount;
                                    cartCountElement.classList.remove('d-none');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification(false, 'Gagal menambahkan produk ke keranjang. Silakan coba lagi.');
                        });
                });
            }
        });

        function showNotification(success, message) {
            const notification = document.getElementById('cart-notification');
            const icon = document.getElementById('cart-notification-icon');
            const title = document.getElementById('cart-notification-title');
            const text = document.getElementById('cart-notification-message');
            const actions = document.getElementById('cart-notification-actions');

            notification.classList.toggle('error', !success);
            icon.className = success ? 'fas fa-check-circle fs-3 me-2 mb-2' : 'fas fa-exclamation-circle fs-3 me-2 mb-2';
            title.textContent = success ? 'Produk berhasil ditambahkan ke keranjang!' : 'Produk gagal ditambahkan';
            text.textContent = success
                ? 'Silakan periksa keranjang belanja Anda untuk melanjutkan checkout.'
                : message;
            // "Lihat Keranjang" only makes sense once something's actually in it
            actions.style.display = success ? 'flex' : 'none';

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
    "image": "{{ \App\Helpers\ImageHelper::url($product->image) }}",
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
    }
    @if ($product->rating_count > 0)
    ,"aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ $product->rating_average }}",
    "reviewCount": "{{ $product->rating_count }}"
    }
    @endif
    }
</script>
@endpush
