<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * View the order and its payment-confirmation flow. Used by
     * OrderController::show(), showPaymentConfirmation(), and
     * processPaymentConfirmation() — previously each repeated this same
     * $order->user_id !== Auth::id() check inline, with no single place
     * that owned the rule.
     */
    public function view(?User $user, Order $order): bool
    {
        return $this->owns($user, $order);
    }

    /**
     * Cancel the order. Ownership only — whether the order's current
     * status still allows cancelling is a separate business rule, and
     * stays in OrderController::cancel() rather than moving here.
     */
    public function cancel(?User $user, Order $order): bool
    {
        return $this->owns($user, $order);
    }

    /**
     * $user nullable sejak #10 (checkout tanpa akun): tamu tidak punya User
     * record untuk dibandingkan, jadi kepemilikan order yang dibuat tamu
     * ($order->user_id === null) dibuktikan lewat session('guest_order_ids')
     * — daftar id order yang SESI INI buat lewat CheckoutController::process()
     * (lihat method itu). Sengaja TIDAK cukup hanya cek
     * "$order->user_id === null" saja — itu akan membiarkan pengunjung
     * anonim mana pun melihat/membatalkan order tamu SIAPA SAJA cukup
     * dengan menebak/menaikkan id order di URL, karena semua order tamu
     * sama-sama punya user_id null.
     */
    private function owns(?User $user, Order $order): bool
    {
        if ($user) {
            return $user->id === $order->user_id;
        }

        return $order->user_id === null
            && in_array($order->id, session('guest_order_ids', []), true);
    }
}
