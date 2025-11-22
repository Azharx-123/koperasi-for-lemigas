<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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

    // Scope untuk produk unggulan
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Accessor untuk format harga
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Mutator untuk slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
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
        return cache()->remember('product_' . $this->id . '_rating_avg', 60 * 24, function () {
            return $this->ratings()->approved()->avg('score') ?: 0;
        });
    }

    // Menghitung jumlah rating
    public function getRatingCountAttribute()
    {
        return cache()->remember('product_' . $this->id . '_rating_count', 60 * 24, function () {
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