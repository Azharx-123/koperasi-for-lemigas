<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format a numeric amount as Indonesian Rupiah, e.g. 1500000 -> "Rp 1.500.000".
     *
     * Centralizes the number_format(...) logic that was previously copy-pasted
     * across Order, OrderItem, and Product accessors.
     */
    public static function formatRupiah(int|float|string|null $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
