<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentConfirmation extends Model
{
    use HasFactory;

    protected $table = 'payment_confirmations';

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
        return CurrencyHelper::formatRupiah($this->amount);
    }

    // Check if payment is verified
    public function getIsVerifiedAttribute()
    {
        return $this->verified_at !== null;
    }

    /**
     * Verify this payment confirmation: mark it verified and move the linked
     * order to paid/processing. Single source of truth for "what does
     * verifying a payment confirmation actually do" — every UI entry point
     * (PaymentConfirmationResource's row action, bulk action, and view page,
     * plus the RelationManager on the order page) calls this instead of
     * each duplicating the same update logic, so they can't drift apart.
     *
     * Already-verified confirmations are a no-op (idempotent), so selecting
     * a mix of verified/unverified rows in a bulk action is harmless.
     *
     * @throws \RuntimeException if the order is no longer in a state that
     *         can accept payment (e.g. already shipped/delivered/cancelled —
     *         forcing it back to "processing" would revive/corrupt it), or
     *         if the transferred amount doesn't match the order total.
     */
    public function verify(): void
    {
        if ($this->is_verified) {
            return;
        }

        $order = $this->order;

        if (! in_array('processing', $order->allowedNextStatuses(), true)) {
            throw new \RuntimeException(
                'Order ' . $order->order_number . ' berstatus "' . (Order::STATUSES[$order->status] ?? $order->status)
                . '" dan tidak bisa diverifikasi pembayarannya lagi.'
            );
        }

        $amountCents = (int) round(((float) $this->amount) * 100);
        $totalCents = (int) round(((float) $order->total) * 100);

        if ($amountCents !== $totalCents) {
            throw new \RuntimeException(
                'Jumlah transfer (' . CurrencyHelper::formatRupiah($this->amount)
                . ') tidak sama dengan total order (' . CurrencyHelper::formatRupiah($order->total)
                . '). Periksa kembali bukti transfer sebelum memverifikasi.'
            );
        }

        DB::transaction(function () use ($order) {
            $this->update(['verified_at' => now()]);
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        });
    }
}
