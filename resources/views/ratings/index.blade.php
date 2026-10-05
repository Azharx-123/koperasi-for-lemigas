<!-- resources/views/ratings/index.blade.php -->

@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/ratings-index.css')
@endpush

@section('title', 'Ulasan ' . $product->name)
@section('meta_description', 'Rating dan ulasan untuk ' . $product->name . '.')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="section-title-elegant">Rating & Ulasan</h2>
                <div class="elegant-separator"></div>

                <div class="product-meta-info mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="product-info">
                            <a href="{{ route('products.show', $product->slug) }}" class="d-flex align-items-center text-decoration-none">
                                <img src="{{ \App\Helpers\ImageHelper::url($product->image) }}" alt="{{ $product->name }}" class="rounded" width="80">
                                <div class="ms-3">
                                    <h4 class="mb-1">{{ $product->name }}</h4>
                                    <div class="text-muted">{{ $product->category->name }}</div>
                                </div>
                            </a>
                        </div>
                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Produk
                        </a>
                    </div>
                </div>

                <!-- Rating Summary (Component) -->
                <x-rating-display :product="$product" />

                <!-- Rating Form for authenticated users -->
                @auth
                    <x-rating-form :product="$product" :userRating="$userRating" />
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Silakan <a href="{{ route('login') }}">login</a> untuk memberikan rating dan ulasan.
                    </div>
                @endauth

                <!-- Reviews List -->
                <h4 class="mb-4">Ulasan dari Pembeli ({{ $ratings->total() }})</h4>

                @if ($ratings->count() > 0)
                    @foreach ($ratings as $rating)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="reviewer-name">{{ $rating->user?->name ?? 'Pengguna' }}</div>
                                        <div class="review-date">{{ $rating->created_at->format('d M Y') }}</div>
                                        @if ($rating->verified_purchase)
                                            <div class="verified-badge mt-1">
                                                <i class="fas fa-check-circle"></i> Pembelian Terverifikasi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="review-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $rating->score)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            @if ($rating->review)
                                <div class="review-content">
                                    {{ $rating->review }}
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($ratings->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $ratings->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-reviews">
                        <div class="empty-reviews-icon">
                            <i class="far fa-comment-dots"></i>
                        </div>
                        <h4>Belum Ada Ulasan</h4>
                        <p class="empty-reviews-text">Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingForm = document.getElementById('ratingForm');

            if (ratingForm) {
                ratingForm.addEventListener('submit', function(e) {
                    const stars = document.querySelector('input[name="score"]:checked');

                    if (!stars) {
                        e.preventDefault();
                        alert('Silakan pilih rating (1-5 bintang)');
                    }
                });
            }
        });
    </script>
@endpush
