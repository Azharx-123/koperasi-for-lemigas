<!-- resources/views/ratings/index.blade.php -->

@extends('layouts.app')

@push('styles')
    <style>
        /* CSS styles dari design sebelumnya */
        .review-item {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .reviewer-info {
            display: flex;
            align-items: center;
        }
        
        .reviewer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgb(var(--gray-200));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            overflow: hidden;
        }
        
        .reviewer-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .reviewer-avatar i {
            font-size: 1.5rem;
            color: rgb(var(--gray-600));
        }
        
        .reviewer-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .review-date {
            font-size: 0.85rem;
            color: rgb(var(--gray-600));
        }
        
        .review-stars {
            color: rgb(var(--yellow-primary));
            font-size: 1.25rem;
        }
        
        .review-content {
            line-height: 1.7;
            color: rgb(var(--gray-800));
        }
        
        .verified-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .verified-badge i {
            margin-right: 0.25rem;
        }
        
        .empty-reviews {
            text-align: center;
            padding: 3rem 0;
        }
        
        .empty-reviews-icon {
            font-size: 4rem;
            color: rgb(var(--gray-300));
            margin-bottom: 1rem;
        }
        
        .empty-reviews-text {
            font-size: 1.25rem;
            color: rgb(var(--gray-600));
            margin-bottom: 2rem;
        }
    </style>
@endpush

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
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="rounded" width="80">
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
                                        <div class="reviewer-name">{{ $rating->user->name }}</div>
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
                    <div class="d-flex justify-content-center mt-4">
                        {{ $ratings->links() }}
                    </div>
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