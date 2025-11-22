<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment_confirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'bank_name',
        'account_name',
        'amount',
        'transfer_date',
        'proof_image',
        'notes',
        'verified_at',
        'admin_notes'
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    // Relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Format currency
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Check if payment is verified
    public function getIsVerifiedAttribute()
    {
        return $this->verified_at !== null;
    }
}
