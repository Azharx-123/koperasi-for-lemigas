@extends('layouts.app')

@push('styles')
    <style>
        /* Essential Base Styles */
        :root {
            --yellow-primary: 255, 193, 7;
            --yellow-dark: 255, 167, 7;
            --gray-100: 248, 249, 250;
            --gray-200: 233, 236, 239;
            --gray-600: 108, 117, 125;
            --blue-badge: 13, 110, 253;
            --orange-badge: 253, 126, 20;
        }

        /* Section Title */
        .section-title-elegant {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
        }

        .elegant-separator {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgb(var(--yellow-primary)) 50%, transparent 100%);
            width: min(150px, 80%);
            margin: 2rem auto;
        }

        /* Filter Section */
        .filter-section {
            background: rgb(var(--gray-100));
            padding: clamp(2rem, 4vw, 4rem) 0;
            margin-bottom: 2rem;
        }

        .filter-card {
            background: white;
            border-radius: 15px;
            padding: clamp(1rem, 2vw, 1.5rem);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .search-input {
            border: 2px solid rgb(var(--gray-200));
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: 0.3s ease;
        }

        .search-input:focus {
            border-color: rgb(var(--yellow-primary));
            box-shadow: 0 0 0 3px rgba(var(--yellow-primary), 0.1);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            padding: 1rem 0;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: 0.3s ease;
            min-height: 360px;
            /* Memastikan tinggi minimum yang sama */
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .product-image-container {
            position: relative;
            padding-top: 70%;
            /* Reduced height ratio */
        }

        .product-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-content {
            padding: 0.8rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-category {
            color: rgb(var(--gray-600));
            font-size: 0.75rem;
            margin-bottom: 0.3rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .product-title {
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            max-height: 4.2rem;
            /* Approximately 3 lines */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .product-description {
            display: none;
            /* Hide description for smaller cards */
        }

        .product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 0.5rem;
        }

        .badge-meta {
            display: inline-flex;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            gap: 0.25rem;
        }

        .badge-rating {
            background-color: rgba(var(--orange-badge), 0.1);
            color: rgb(var(--orange-badge));
        }

        .badge-sales {
            background-color: rgba(var(--blue-badge), 0.1);
            color: rgb(var(--blue-badge));
        }

        .product-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: rgb(var(--yellow-dark));
            margin-bottom: 0.5rem;
        }

        .btn-product {
            background: rgb(var(--yellow-primary));
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: block;
            border: none;
        }

        .product-info {
            flex: 1;
        }

        .product-data {
            margin-top: 0.5rem;
        }

        .btn-detail {
            font-size: 0.85rem;
            padding: 0.5rem 0;
            border-radius: 8px;
        }

        .btn-product:hover {
            background: rgb(var(--yellow-dark));
            transform: translateY(-2px);
            color: white;
        }

        /* Responsive Form Grid */

        /* Responsive Filter Card */
        @media (max-width: 992px) {
            .filter-card .row {
                row-gap: 1rem;
            }

            .filter-card [class*="col-"] {
                width: 100%;
            }

            .filter-card .btn-product {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .filter-card .row {
                row-gap: 1rem;
            }

            .filter-card [class*="col-"] {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Semua Produk Lengkap Disini</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="filter-card">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control search-input"
                                placeholder="Cari produk..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select search-input">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="sort" class="form-select search-input">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                    Terbaru
                                </option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                                    Harga Terendah
                                </option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                    Harga Tertinggi
                                </option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                                    Terpopuler
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-product w-100">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <div class="container">
        <div class="product-grid">
            @foreach ($products as $product)
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-image">
                    </div>
                    <div class="product-content">
                        <div class="product-info">
                            <div class="product-category">{{ $product->category->name }}</div>
                            <h3 class="product-title">{{ $product->name }}</h3>
                        </div>
                        <div class="product-data">
                            <div class="product-meta">
                                <span class="badge-meta badge-rating">
                                    <i class="fas fa-star"></i>
                                    {{ number_format($product->rating_average, 1) }}
                                    ({{ $product->rating_count }})
                                </span>
                                <span class="badge-meta badge-sales">
                                    <i class="fas fa-shopping-cart"></i>
                                    {{ $product->sold_count }}+
                                </span>
                            </div>
                            <div class="product-price">{{ $product->formatted_price }}</div>
                            <a href="{{ route('products.show', $product->slug) }}"
                                class="btn btn-outline-dark w-100 btn-detail">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
@endsection
