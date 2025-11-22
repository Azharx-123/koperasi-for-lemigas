<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Order_item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        // Get cart items and total
        $cart = $this->getCart();

        if (!$cart || count($cart->products) === 0) {
            return redirect()->route('cart.show')->with('error', 'Keranjang belanja Anda kosong');
        }

        $cartItems = [];

        foreach ($cart->products as $product) {
            $cartItems[$product->pivot->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->pivot->price,
                'quantity' => $product->pivot->quantity,
                'image' => $product->image,
                'pivot_id' => $product->pivot->id
            ];
        }

        $total = $cart->getTotal();

        // Calculate shipping fee (could be dynamic based on address)
        $shipping_fee = 25000; // Rp 25.000 default shipping

        // Calculate tax
        $tax = $total * 0.11; // 11% tax

        return view('checkout.index', compact('cartItems', 'total', 'shipping_fee', 'tax'));
    }

    public function process(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'payment_method' => 'required|in:bank_transfer,credit_card,ewallet',
        ]);

        // Get cart
        $cart = $this->getCart();

        if (!$cart || count($cart->products) === 0) {
            return redirect()->route('cart.show')->with('error', 'Keranjang belanja Anda kosong');
        }

        // Calculate totals
        $subtotal = $cart->getTotal();
        $shipping_fee = 25000; // Default shipping
        $tax = $subtotal * 0.11; // 11% tax
        $total = $subtotal + $shipping_fee + $tax;

        try {
            // Begin transaction
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . time() . '-' . Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shipping_fee,
                'tax' => $tax,
                'total' => $total,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'notes' => $request->shipping_notes,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending'
            ]);

            // Create order items
            foreach ($cart->products as $product) {
                Order_item::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                    'total' => $product->pivot->quantity * $product->pivot->price
                ]);
            }

            // Clear cart
            $cart->products()->detach();

            // Commit transaction
            DB::commit();

            // Redirect to thank you page or payment process
            return redirect()->route('checkout.complete', $order->id)->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();

            // Log error
            Log::error('Checkout Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    public function complete($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Only allow the order owner to access
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.complete', compact('order'));
    }

    private function getCart()
    {
        $user = Auth::user();
        return $user->cart;
    }
}
