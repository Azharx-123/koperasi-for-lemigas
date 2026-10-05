<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Single source of truth for product status labels — used by
     * ProductResource's form select, table column, and filter instead of
     * each re-declaring the same array.
     */
    public const STATUSES = [
        'active' => 'Active',
        'draft' => 'Draft',
        'inactive' => 'Inactive',
    ];

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'image',
        'sold_count',
        'stock',
        'category_id',
        'status',
        'featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
    ];

    // Relationship dengan category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Scope untuk produk aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Whether this product can currently be added to a cart / checked out.
     * Single source of truth for that rule — CartController::add() and
     * CheckoutController::process() both call this instead of separately
     * (and, as happened before, incompletely) re-deriving it, e.g. only
     * checking stock and forgetting status.
     */
    public function isPurchasable(): bool
    {
        return $this->status === 'active';
    }

    // Scope untuk produk unggulan
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Eager-load the approved rating average/count for a batch of products in
     * a single query (via SQL subqueries), instead of every product hitting
     * cache()->remember() individually when rating_average/rating_count are
     * read in a loop — e.g. products.index and products.show's related
     * products. getRatingAverageAttribute()/getRatingCountAttribute() below
     * pick these up automatically when present.
     */
    public function scopeWithRatingAggregates($query)
    {
        return $query
            ->withCount(['ratings as rating_count_computed' => fn ($q) => $q->approved()])
            ->withAvg(['ratings as rating_avg_computed' => fn ($q) => $q->approved()], 'score');
    }

    // Accessor untuk format harga
    public function getFormattedPriceAttribute()
    {
        return CurrencyHelper::formatRupiah($this->price);
    }

    /**
     * Whether this product should show the "New" badge.
     * 14 days is a starting point; adjust if a different window fits better.
     */
    public function getIsNewAttribute(): bool
    {
        return $this->created_at !== null && $this->created_at->gt(now()->subDays(14));
    }

    // Mutator untuk slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // ProductResource's slug field lets an admin type their own value
            // (it only auto-fills from the name via JS on the create form), so
            // use whatever was submitted as the source and just guarantee it's
            // unique. If it's empty (e.g. created outside the form), fall back
            // to deriving it from the name.
            $product->slug = static::generateUniqueSlug($product->slug ?: $product->name);
        });

        static::updating(function ($product) {
            // Only re-derive the slug from the new name when nobody explicitly
            // edited the slug field in this same save — an admin-chosen slug
            // should never be silently overwritten just because the name changed.
            if ($product->isDirty('slug')) {
                $product->slug = static::generateUniqueSlug($product->slug, $product->id);
            } elseif ($product->isDirty('name')) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    /**
     * Turn $source into a slug guaranteed to be unique among products,
     * appending -2, -3, ... as needed. Includes soft-deleted rows so a
     * deleted product's old slug can't collide with a new one.
     */
    protected static function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $slug = Str::slug($source);
        $original = $slug;
        $suffix = 2;

        while (
            static::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class)
            ->withPivot('quantity', 'price', 'id')
            ->withTimestamps();
    }

    // Relasi dengan ratings
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Menghitung rata-rata rating (dengan caching)
    public function getRatingAverageAttribute()
    {
        // Sudah di-eager-load lewat scopeWithRatingAggregates() (dipakai di
        // ProductController)? Pakai langsung, tanpa query tambahan.
        if (array_key_exists('rating_avg_computed', $this->attributes)) {
            return $this->attributes['rating_avg_computed'] ?? 0;
        }

        // Cache::remember()'s TTL argument has taken seconds (not minutes)
        // since Laravel 5.8, so this uses now()->addDay() directly rather
        // than a raw number, to keep the intent unambiguous.
        return cache()->remember('product_' . $this->id . '_rating_avg', now()->addDay(), function () {
            return $this->ratings()->approved()->avg('score') ?: 0;
        });
    }

    // Menghitung jumlah rating
    public function getRatingCountAttribute()
    {
        if (array_key_exists('rating_count_computed', $this->attributes)) {
            return (int) $this->attributes['rating_count_computed'];
        }

        return cache()->remember('product_' . $this->id . '_rating_count', now()->addDay(), function () {
            return $this->ratings()->approved()->count();
        });
    }

    // Method untuk refresh cache rating
    public function refreshRatingCache()
    {
        cache()->forget('product_' . $this->id . '_rating_avg');
        cache()->forget('product_' . $this->id . '_rating_count');

        // Memanggil accessor untuk mengisi ulang cache
        $this->rating_average;
        $this->rating_count;

        return $this;
    }
}
