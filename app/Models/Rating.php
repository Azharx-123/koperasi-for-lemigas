<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_item_id',
        'score',
        'review',
        'verified_purchase',
        'is_approved',
    ];

    protected $casts = [
        'score' => 'integer',
        'verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
    ];

    /**
     * Keep the affected product's cached rating_average/rating_count in
     * sync with whatever ratings actually exist and are approved. Single
     * source of truth: the public rating form and every admin panel action
     * (approve, reject, edit, delete, bulk delete) all get this for free
     * instead of each needing to remember to call refreshRatingCache()
     * itself, which would otherwise risk leaving a stale cached average
     * behind after actions like a delete.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function (Rating $rating) {
            $rating->product?->refreshRatingCache();
        });

        static::updated(function (Rating $rating) {
            if ($rating->wasChanged('product_id')) {
                // Rating moved to a different product — the one it left
                // behind has a stale cache too, not just the new one.
                Product::find($rating->getOriginal('product_id'))?->refreshRatingCache();
            }

            if ($rating->wasChanged(['score', 'is_approved', 'product_id'])) {
                $rating->product?->refreshRatingCache();
            }
        });

        static::deleted(function (Rating $rating) {
            $rating->product?->refreshRatingCache();
        });
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke order_item
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Scope untuk rating yang sudah diapprove
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // Scope untuk verified purchase
    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }
}
