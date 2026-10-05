<style>
    .rating-form-container {
        background: white;
        border-radius: var(--radius-panel);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .rating-form-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: var(--spacing-md);
        color: rgb(var(--gray-800));
        border-bottom: 2px solid rgb(var(--yellow-primary));
        padding-bottom: var(--spacing-xs);
        display: inline-block;
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        font-size: 2rem;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        cursor: pointer;
        color: rgb(var(--gray-300));
        padding: 0 0.2rem;
        transition: all 0.3s ease;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: rgb(var(--yellow-primary));
    }

    .rating-label {
        margin-top: var(--spacing-xs);
        font-size: 0.9rem;
        color: rgb(var(--gray-600));
    }

    .btn-submit-rating {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
        color: white;
        font-weight: 600;
        font-size: 1rem;
        border-radius: var(--radius-pill);
        border: none;
        box-shadow: var(--shadow-yellow-glow);
        transition: all 0.3s ease;
    }

    .btn-submit-rating:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-yellow-glow-lg);
    }
</style>

<div class="rating-form-container mt-4">
    <h4 class="rating-form-title">Beri Rating & Ulasan</h4>

    <form action="{{ route('ratings.store', $product) }}" method="POST" id="ratingForm" class="rating-form">
        @csrf
        <div class="rating-stars mb-3">
            <div class="star-rating">
                @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" id="star{{ $i }}" name="score" value="{{ $i }}" {{ old('score', $userRating->score ?? 0) == $i ? 'checked' : '' }}>
                    <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                @endfor
            </div>
            <div class="rating-label">Pilih Rating (1-5)</div>
        </div>

        <div class="form-group mb-3">
            <label for="review" class="form-label">Ulasan Anda (Opsional)</label>
            <textarea class="form-control" id="review" name="review" rows="4" placeholder="Bagikan pengalaman Anda dengan produk ini...">{{ old('review', $userRating->review ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn-submit-rating">
            <i class="fas fa-paper-plane me-2"></i>Kirim Rating & Ulasan
        </button>
    </form>
</div>
