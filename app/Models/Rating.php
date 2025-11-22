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
        return $this->belongsTo(Order_item::class);
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
