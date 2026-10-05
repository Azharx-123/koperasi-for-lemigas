<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * When true, the create/update/delete hooks below skip stock/sold_count
     * adjustment and order-total recalculation entirely. Set via
     * withoutAutoSync() — see its docblock.
     */
    protected static bool $suppressSync = false;

    /**
     * Run $callback with automatic stock/order-total syncing suspended for
     * any OrderItem created/updated/deleted inside it.
     *
     * CheckoutController is the one place that needs this: it already
     * deducts stock itself with an atomic, race-condition-safe
     * `UPDATE ... WHERE stock >= quantity` (the generic adjustStock() below
     * has no such guard — it's a plain decrement, fine for a trusted admin
     * correcting one order, not safe for concurrent public checkouts
     * racing over the last unit), and it sets the new order's
     * subtotal/tax/total once upfront from the cart total. Letting the
     * hooks below also run for those same order items would deduct stock
     * twice and overwrite already-correct totals with redundant work.
     *
     * Every other creator of OrderItem rows (ItemsRelationManager,
     * OrderItemResource, or any future admin-side use) does not call this,
     * and keeps syncing automatically — that's what keeps admin edits to
     * order items correctly reflected in stock and order totals.
     */
    public static function withoutAutoSync(\Closure $callback): mixed
    {
        $previous = static::$suppressSync;
        static::$suppressSync = true;

        try {
            return $callback();
        } finally {
            static::$suppressSync = $previous;
        }
    }

    /**
     * Keep this item's own total, its product's stock/sold_count, and its
     * parent order's subtotal/tax/total all in sync with reality — no
     * matter which admin UI (ItemsRelationManager, the standalone
     * OrderItemResource) triggers the change. This is the single source of
     * truth for "what happens when an order item is added/edited/removed",
     * so admin edits to order items always keep order totals and product
     * stock correctly in sync.
     */
    protected static function boot()
    {
        parent::boot();

        // total is derived, never trusted from form input — OrderItemResource's
        // "total" field is only disabled client-side, so a submitted value
        // could still be tampered with before it reaches the server.
        static::saving(function (OrderItem $item) {
            $item->total = round(((float) $item->price) * (int) $item->quantity, 2);

            // Snapshot the product's name once, so historical orders keep
            // showing what was actually bought even if the product is later
            // renamed or soft-deleted. withTrashed() so a since-deleted
            // product can still be captured. Re-snapshotted only when
            // product_id itself changes (e.g. an admin repoints this item
            // at a different product) — a plain quantity/price edit doesn't
            // touch it, and a later rename of the same product doesn't
            // silently rewrite past order history.
            if ($item->product_id && (blank($item->product_name) || $item->isDirty('product_id'))) {
                $item->product_name = Product::withTrashed()->find($item->product_id)?->name;
            }
        });

        static::created(function (OrderItem $item) {
            if (static::$suppressSync) {
                return;
            }

            DB::transaction(function () use ($item) {
                static::adjustStock($item->product_id, -$item->quantity);
                $item->order?->recalculateTotals();
            });
        });

        static::updated(function (OrderItem $item) {
            if (static::$suppressSync) {
                return;
            }

            DB::transaction(function () use ($item) {
                if ($item->wasChanged('product_id')) {
                    // Put the old product's stock back, then deduct from the new one.
                    static::adjustStock($item->getOriginal('product_id'), $item->getOriginal('quantity'));
                    static::adjustStock($item->product_id, -$item->quantity);
                } elseif ($item->wasChanged('quantity')) {
                    static::adjustStock($item->product_id, $item->getOriginal('quantity') - $item->quantity);
                }

                if ($item->wasChanged('order_id')) {
                    // Item moved to a different order — the order it left
                    // also needs its totals recalculated, not just the one
                    // it joined.
                    Order::find($item->getOriginal('order_id'))?->recalculateTotals();
                }

                if ($item->wasChanged(['order_id', 'product_id', 'quantity', 'price'])) {
                    $item->order?->recalculateTotals();
                }
            });
        });

        static::deleted(function (OrderItem $item) {
            if (static::$suppressSync) {
                return;
            }

            DB::transaction(function () use ($item) {
                static::adjustStock($item->product_id, $item->quantity);
                $item->order?->recalculateTotals();
            });
        });
    }

    /**
     * Move a product's stock by $delta (positive = restore, negative =
     * deduct) and move sold_count the opposite way. Mirrors the same pair
     * of increment/decrement calls CheckoutController (checkout deduction)
     * and Order::cancelAndRestoreStock() (cancellation) already use, so all
     * three stay consistent with each other.
     */
    protected static function adjustStock(?int $productId, int $delta): void
    {
        if (! $productId || $delta === 0) {
            return;
        }

        if ($delta > 0) {
            Product::where('id', $productId)->increment('stock', $delta);
            Product::where('id', $productId)->decrement('sold_count', $delta);
        } else {
            Product::where('id', $productId)->decrement('stock', abs($delta));
            Product::where('id', $productId)->increment('sold_count', abs($delta));
        }
    }

    // Relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Format currency
    public function getFormattedPriceAttribute()
    {
        return CurrencyHelper::formatRupiah($this->price);
    }

    public function getFormattedTotalAttribute()
    {
        return CurrencyHelper::formatRupiah($this->total);
    }

    /**
     * The name to show for this line item: the snapshot taken at purchase
     * time, falling back to the product's current name (covers rows saved
     * before this snapshot column existed), and finally a placeholder if
     * neither is available. Single source of truth for both the admin
     * panel and the customer-facing order page, so they can't disagree.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->product_name
            ?: $this->product?->name
            ?: 'Produk tidak tersedia';
    }
}
