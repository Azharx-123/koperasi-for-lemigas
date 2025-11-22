<!-- resources/views/components/rating-display.blade.php -->

<div class="ratings-display">
    <div class="rating-summary">
        <div class="rating-average">
            <div class="average-score">{{ number_format($product->rating_average, 1) }}</div>
            <div class="average-stars">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= round($product->rating_average))
                        <i class="fas fa-star"></i>
                    @elseif ($i - 0.5 <= $product->rating_average)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <div class="rating-count">{{ $product->rating_count }} ulasan</div>
        </div>
        
        <div class="rating-distribution">
            @php
                $ratingCounts = DB::table('ratings')
                    ->where('product_id', $product->id)
                    ->where('is_approved', true)
                    ->select(DB::raw('score, count(*) as count'))
                    ->groupBy('score')
                    ->orderBy('score', 'desc')
                    ->get()
                    ->pluck('count', 'score')
                    ->toArray();
                
                $maxCount = max($ratingCounts ?: [0]);
            @endphp
            
            @for ($i = 5; $i >= 1; $i--)
                <div class="rating-bar">
                    <div class="rating-label">{{ $i }} <i class="fas fa-star"></i></div>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $maxCount ? ($ratingCounts[$i] ?? 0) / $maxCount * 100 : 0 }}%; background-color: rgb(var(--yellow-primary))"></div>
                    </div>
                    <div class="rating-count">{{ $ratingCounts[$i] ?? 0 }}</div>
                </div>
            @endfor
        </div>
    </div>
    
    <div class="rating-filter">
        <a href="{{ route('ratings.index', $product) }}" class="btn-filter-rating {{ !request()->has('filter') ? 'active' : '' }}">
            Semua Ulasan
        </a>
        <a href="{{ route('ratings.index', ['product' => $product, 'filter' => 'verified']) }}" class="btn-filter-rating {{ request('filter') == 'verified' ? 'active' : '' }}">
            Pembelian Terverifikasi
        </a>
    </div>
</div>

<style>
    .ratings-display {
        margin-bottom: 2rem;
    }
    
    .rating-summary {
        display: flex;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }
    
    .rating-average {
        text-align: center;
        padding: 1.5rem;
        background-color: rgba(var(--yellow-primary), 0.05);
        border-radius: 15px;
        min-width: 200px;
    }
    
    .average-score {
        font-size: 3rem;
        font-weight: 700;
        color: rgb(var(--yellow-dark));
    }
    
    .average-stars {
        color: rgb(var(--yellow-primary));
        font-size: 1.5rem;
        margin: 0.5rem 0;
    }
    
    .rating-count {
        color: rgb(var(--gray-600));
    }
    
    .rating-distribution {
        flex: 1;
        padding-top: 1rem;
    }
    
    .rating-bar {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .rating-label {
        min-width: 70px;
        color: rgb(var(--gray-800));
    }
    
    .progress {
        flex: 1;
        height: 12px;
        background-color: rgb(var(--gray-200));
        border-radius: 10px;
        margin: 0 1rem;
        overflow: hidden;
    }
    
    .progress-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    
    .rating-filter {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .btn-filter-rating {
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        border: 2px solid rgb(var(--gray-300));
        color: rgb(var(--gray-800));
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-filter-rating:hover, .btn-filter-rating.active {
        background-color: rgb(var(--yellow-primary));
        border-color: rgb(var(--yellow-primary));
        color: rgb(var(--black));
    }
    
    @media (max-width: 768px) {
        .rating-summary {
            flex-direction: column;
            gap: 1rem;
        }
        
        .rating-average {
            min-width: auto;
        }
    }
</style>