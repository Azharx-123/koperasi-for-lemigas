<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
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
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        // Eager load related data
        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    /**
     * Display the payment confirmation form.
     */
    public function showPaymentConfirmation(Order $order)
    {
        $this->authorize('view', $order);

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
        $this->authorize('view', $order);

        // Check if the order still needs payment confirmation. Without this,
        // submitting the form twice (double-click, two open tabs, or back
        // button after an earlier submission) creates a second
        // payment_confirmations row for the same order. Mirrors the check
        // showPaymentConfirmation() above already does before showing the form.
        if ($order->payment_status !== 'pending' || $order->payment_method !== 'bank_transfer') {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan ini tidak memerlukan konfirmasi pembayaran.');
        }

        // Validate request
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:' . $order->total . '|max:' . $order->total,
            'transfer_date' => 'required|date',
            'proof_image' => 'required|image|max:2048', // Max 2MB
        ]);

        // Store the payment proof image. Resized/re-encoded (not just
        // ->store()'d as-is) — kept at a higher quality/size than product
        // photos since this needs to stay legible for the admin to verify
        // amounts and reference numbers.
        $imagePath = ImageHelper::optimizeAndStore(
            $request->file('proof_image'),
            'payment_proofs',
            maxWidth: 1600,
            maxHeight: 1600,
            quality: 85,
        );

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
        $this->authorize('cancel', $order);

        // Check if the order can be canceled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        // Cancel the order: restores each item's stock and reverses
        // sold_count, then marks the order cancelled (all in one transaction).
        $order->cancelAndRestoreStock();

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
