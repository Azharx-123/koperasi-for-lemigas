<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'id')
            ->withTimestamps();
    }

    public function getTotal()
    {
        // If the caller already eager-loaded products (e.g. CartController::show()
        // iterating them for display), reuse that in-memory collection for free.
        if ($this->relationLoaded('products')) {
            return $this->products->sum(function ($product) {
                return $product->pivot->price * $product->pivot->quantity;
            });
        }

        // Otherwise, don't hydrate every full Product row just to sum two
        // pivot columns — aggregate it in the database instead.
        return (float) $this->products()
            ->selectRaw('SUM(cart_product.price * cart_product.quantity) as total')
            ->value('total') ?? 0.0;
    }

    public function getItemCount()
    {
        if ($this->relationLoaded('products')) {
            return $this->products->sum(function ($product) {
                return $product->pivot->quantity;
            });
        }

        return (int) $this->products()->sum('cart_product.quantity');
    }
}
