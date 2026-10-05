@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/products-index.css')
@endpush

@section('title', 'Produk')
@section('meta_description', 'Jelajahi katalog produk ' . $company->name . '.')

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

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Semua Produk Lengkap Disini</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="filter-card">
                <form action="{{ route('products.index') }}" method="GET" id="filter-form">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="filter-search" class="visually-hidden">Cari produk</label>
                            <input type="text" name="search" id="filter-search" class="form-control search-input"
                                placeholder="Cari produk..." value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label for="filter-category" class="visually-hidden">Kategori</label>
                            <select name="category" id="filter-category" class="form-select search-input">
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
                            <label for="filter-sort" class="visually-hidden">Urutkan</label>
                            <select name="sort" id="filter-sort" class="form-select search-input">
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
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <div class="container">
        <div class="product-grid">
            @forelse ($products as $product)
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="{{ \App\Helpers\ImageHelper::url($product->image) }}" alt="{{ $product->name }}" class="product-image">
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
            @empty
                <div class="empty-products">
                    <div class="empty-products-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="empty-products-title">Produk Tidak Ditemukan</h3>
                    @if (request()->filled('search'))
                        <p class="empty-products-text">Tidak ada produk yang cocok dengan kata kunci
                            "<strong>{{ request('search') }}</strong>". Coba kata kunci lain atau ubah filter
                            kategori yang digunakan.</p>
                    @else
                        <p class="empty-products-text">Coba ubah kata kunci pencarian atau filter kategori yang digunakan.</p>
                    @endif
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark">
                        <i class="fas fa-undo me-2"></i>Reset Filter
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        {{ $products->links('vendor.pagination.kep') }}
    </div>
@endsection

@push('scripts')
    <script>
        // Filter-card kini aktif sendiri: dropdown submit begitu diubah,
        // kotak pencarian submit sesaat setelah berhenti mengetik — tombol
        // "Filter" yang lama sudah dihapus dari markup di atas.
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filter-form');
            if (!form) return;

            document.getElementById('filter-category')?.addEventListener('change', () => form.requestSubmit());
            document.getElementById('filter-sort')?.addEventListener('change', () => form.requestSubmit());

            let searchTimer = null;
            document.getElementById('filter-search')?.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => form.requestSubmit(), 600);
            });
        });
    </script>
@endpush
