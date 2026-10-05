<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Human-readable labels for the `status` column, keyed by the raw value
     * stored in the database. Single source of truth: App\Filament\Resources\OrderResource
     * reads from these same constants instead of keeping its own copies, so the
     * form, table, filters, and infolist can never drift out of sync with each other.
     */
    public const STATUSES = [
        'pending' => 'Menunggu',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Diterima',
        'cancelled' => 'Dibatalkan',
    ];

    /** Filament badge color per status (warning/info/primary/success/danger). */
    public const STATUS_COLORS = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
    ];

    /**
     * Statuses an order is allowed to move to *from* a given current status
     * (a status always includes itself, so "leave unchanged" is valid).
     * Mirrors the transitions already implied by the action buttons on the
     * Filament "View Order" page (pending/processing -> shipped -> delivered,
     * pending/processing -> cancelled). Used by OrderResource's edit form so
     * an admin can't pick an impossible transition, e.g. moving a cancelled
     * order (whose stock has already been restored) back to pending.
     */
    public const STATUS_TRANSITIONS = [
        'pending' => ['pending', 'processing', 'shipped', 'cancelled'],
        'processing' => ['processing', 'shipped', 'cancelled'],
        'shipped' => ['shipped', 'delivered'],
        'delivered' => ['delivered'],
        'cancelled' => ['cancelled'],
    ];

    /** Human-readable labels for the `payment_status` column. */
    public const PAYMENT_STATUSES = [
        'pending' => 'Menunggu Pembayaran',
        'processing' => 'Memproses Pembayaran',
        'paid' => 'Lunas',
        'failed' => 'Gagal',
        'refunded' => 'Dikembalikan',
    ];

    /** Filament badge color per payment status. */
    public const PAYMENT_STATUS_COLORS = [
        'pending' => 'warning',
        'processing' => 'info',
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'info',
    ];

    /**
     * Human-readable labels for the `payment_method` column. bank_transfer,
     * qris, and ewallet are all simulated — no real payment gateway is
     * wired in for any of them — but they differ in HOW: bank_transfer
     * stays 'pending' until the customer uploads proof of transfer and an
     * admin verifies it manually (see orders.payment.confirmation), while
     * qris/ewallet are marked 'paid' immediately by
     * CheckoutController::process() (see #12 in CATATAN-LANJUTAN-REVISI.md).
     * credit_card is deliberately NOT listed: a believable simulation of it
     * would need real gateway concepts (card tokenisation, 3DS) this app
     * doesn't implement, unlike QRIS/E-Wallet which can honestly be
     * simulated as "paid instantly". OrderResource's payment_method
     * Select/infolist read their choices straight from this constant.
     * getPaymentMethodLabelAttribute() below still falls back to ucfirst()
     * for any order carrying a value outside this list, so it never
     * displays as a blank/missing label.
     */
    public const PAYMENT_METHODS = [
        'bank_transfer' => 'Transfer Bank',
        'qris' => 'QRIS',
        'ewallet' => 'E-Wallet',
    ];

    /**
     * Tax rate applied to subtotal (11%). Single source of truth for
     * CheckoutController (order creation) and recalculateTotals() (admin
     * edits to order items), so the rate can't drift between the two.
     */
    public const TAX_RATE = 0.11;

    /**
     * Flat shipping fee in Rupiah. Single source of truth for
     * CheckoutController::index() and ::process(), which previously each
     * hardcoded their own copy of 25000 — same drift risk TAX_RATE avoids.
     * Flagged as "could be dynamic based on address" in the original code;
     * still flat for now, but this is the one place to change that.
     */
    public const SHIPPING_FEE = 25000;

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with OrderItems
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Get formatted order status (Bootstrap badge, for Blade views)
    public function getStatusLabelAttribute()
    {
        return $this->renderStatusBadge(self::STATUSES, self::STATUS_COLORS, $this->status);
    }

    // Get formatted payment status (Bootstrap badge, for Blade views)
    public function getPaymentStatusLabelAttribute()
    {
        return $this->renderStatusBadge(self::PAYMENT_STATUSES, self::PAYMENT_STATUS_COLORS, $this->payment_status);
    }

    // Get formatted payment method
    public function getPaymentMethodLabelAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? ucfirst((string) $this->payment_method);
    }

    /**
     * Build a Bootstrap badge <span> for a status value. Translates the
     * shared Filament color token (warning/info/primary/success/danger)
     * into the matching Bootstrap classes, so the color itself only has
     * to be defined once (in STATUS_COLORS / PAYMENT_STATUS_COLORS) and
     * is reused by both the admin panel and the public-facing views.
     */
    protected function renderStatusBadge(array $labels, array $colors, ?string $value): string
    {
        $bootstrapClasses = [
            'warning' => 'bg-warning text-dark',
            'info' => 'bg-info text-dark',
            'primary' => 'bg-primary text-white',
            'success' => 'bg-success text-white',
            'danger' => 'bg-danger text-white',
        ];

        $class = $bootstrapClasses[$colors[$value] ?? null] ?? 'bg-secondary text-white';
        $label = $labels[$value] ?? ucfirst((string) $value);

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    // Format currency
    public function getFormattedTotalAttribute()
    {
        return CurrencyHelper::formatRupiah($this->total);
    }

    public function getFormattedSubtotalAttribute()
    {
        return CurrencyHelper::formatRupiah($this->subtotal);
    }

    public function getFormattedShippingAttribute()
    {
        return CurrencyHelper::formatRupiah($this->shipping);
    }

    public function getFormattedTaxAttribute()
    {
        return CurrencyHelper::formatRupiah($this->tax);
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
        return $this->hasOne(PaymentConfirmation::class);
    }

    /**
     * Statuses this order can legally move to right now (including staying
     * the same), based on STATUS_TRANSITIONS. Falls back to only allowing
     * the current status if it's somehow outside the known list.
     */
    public function allowedNextStatuses(): array
    {
        return self::STATUS_TRANSITIONS[$this->status] ?? [$this->status];
    }

    /**
     * Cancel this order: restore each item's product stock, reverse its
     * sold_count, and mark the order cancelled — all in one transaction.
     * This is the single source of truth for cancellation, used by both
     * OrderController (web) and Filament's ViewOrder page, so the two
     * stay in sync with each other.
     */
    public function cancelAndRestoreStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                Product::where('id', $item->product_id)->decrement('sold_count', $item->quantity);
            }

            // Reset payment_status too — a payment that was pending/processing
            // for this order no longer applies to anything once it's
            // cancelled. Leave a 'paid' payment_status alone: cancelling an
            // order that's already been paid is a refund, a different flow
            // this method isn't meant to handle.
            $this->update([
                'status' => 'cancelled',
                'payment_status' => $this->payment_status === 'paid' ? $this->payment_status : 'failed',
            ]);
        });
    }

    /**
     * Recalculate subtotal/tax/total from this order's current items, using
     * the same formula CheckoutController applies at checkout time
     * (tax = subtotal * TAX_RATE, total = subtotal + shipping + tax).
     * Shipping is left untouched since it isn't derived from items.
     *
     * Called automatically whenever an OrderItem belonging to this order is
     * created, updated, or deleted (see OrderItem::boot()) — single source
     * of truth, so admin edits via ItemsRelationManager or the standalone
     * OrderItemResource can't leave subtotal/tax/total out of sync with
     * what the order actually contains.
     */
    public function recalculateTotals(): void
    {
        $subtotal = round((float) $this->items()->sum('total'), 2);
        $tax = round($subtotal * self::TAX_RATE, 2);
        $total = round($subtotal + (float) $this->shipping + $tax, 2);

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
}
