<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'shipping',
        'tax',
        'total',
        'name',
        'email',
        'phone',
        'company',
        'address',
        'province',
        'city',
        'district',
        'postal_code',
        'notes',
        'payment_method',
        'payment_status',
        'tracking_number',
        'shipped_at'
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with OrderItems
    public function items()
    {
        return $this->hasMany(Order_item::class);
    }

    // Relationship with Products through OrderItems
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price', 'total')
            ->withTimestamps();
    }

    // Get formatted order status
    public function getStatusLabelAttribute()
    {
        $statusClasses = [
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info text-dark',
            'shipped' => 'bg-primary text-white',
            'delivered' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
        ];

        $statusLabels = [
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'cancelled' => 'Dibatalkan',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-secondary text-white';
        $label = $statusLabels[$this->status] ?? ucfirst($this->status);

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    // Get formatted payment status
    public function getPaymentStatusLabelAttribute()
    {
        $statusClasses = [
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info text-dark',
            'paid' => 'bg-success text-white',
            'failed' => 'bg-danger text-white',
            'refunded' => 'bg-info text-white',
        ];

        $statusLabels = [
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Memproses Pembayaran',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'refunded' => 'Dikembalikan',
        ];

        $class = $statusClasses[$this->payment_status] ?? 'bg-secondary text-white';
        $label = $statusLabels[$this->payment_status] ?? ucfirst($this->payment_status);

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    // Get formatted payment method
    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'bank_transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'ewallet' => 'E-Wallet',
        ];

        return $methods[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    // Format currency
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedShippingAttribute()
    {
        return 'Rp ' . number_format($this->shipping, 0, ',', '.');
    }

    public function getFormattedTaxAttribute()
    {
        return 'Rp ' . number_format($this->tax, 0, ',', '.');
    }

    // Scope for user orders
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Count items in order
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    // Relationship with PaymentConfirmation
    public function paymentConfirmation()
    {
        return $this->hasOne(Payment_confirmation::class);
    }
}
