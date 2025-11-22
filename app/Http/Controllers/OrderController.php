<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::forUser(Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load related data
        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    /**
     * Display the payment confirmation form.
     */
    public function showPaymentConfirmation(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order needs payment confirmation
        if ($order->payment_status !== 'pending' || $order->payment_method !== 'bank_transfer') {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan ini tidak memerlukan konfirmasi pembayaran.');
        }

        return view('orders.payment_confirmation', compact('order'));
    }

    /**
     * Process the payment confirmation.
     */
    public function processPaymentConfirmation(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate request
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:' . $order->total,
            'transfer_date' => 'required|date',
            'proof_image' => 'required|image|max:2048', // Max 2MB
        ]);

        // Store the payment proof image
        $imagePath = $request->file('proof_image')->store('payment_proofs', 'public');

        // Create payment confirmation record
        $order->paymentConfirmation()->create([
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'amount' => $request->amount,
            'transfer_date' => $request->transfer_date,
            'proof_image' => $imagePath,
            'notes' => $request->notes,
        ]);

        // Update order status (optional, you might want admin to verify first)
        $order->update([
            'status' => 'processing',
            'payment_status' => 'processing', // Use 'processing' status until admin verifies
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Konfirmasi pembayaran berhasil dikirim. Tim kami akan memverifikasi pembayaran Anda dalam 1x24 jam kerja.');
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order can be canceled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        // Update order status
        $order->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
