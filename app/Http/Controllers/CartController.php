<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        // Get or create user's cart
        $cart = $this->getOrCreateCart();

        // Check if product already exists in cart
        $existingProduct = $cart->products()->where('product_id', $product->id)->first();

        if ($existingProduct) {
            // Update quantity if product already in cart
            $newQuantity = $existingProduct->pivot->quantity + $request->quantity;
            $cart->products()->updateExistingPivot($product->id, [
                'quantity' => $newQuantity
            ]);
        } else {
            // Add new product to cart
            $cart->products()->attach($product->id, [
                'quantity' => $request->quantity,
                'price' => $product->price
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cartCount' => Cart::count(), // Sesuaikan dengan cara Anda menghitung jumlah item di keranjang
                'cartTotal' => Cart::total() // Opsional: total harga keranjang
            ]);
        }
    }

    public function show()
    {
        $cart = $this->getOrCreateCart();
        $cartItems = [];

        foreach ($cart->products as $product) {
            // Here we're adding the stock information
            $cartItems[$product->pivot->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->pivot->price,
                'quantity' => $product->pivot->quantity,
                'image' => $product->image,
                'pivot_id' => $product->pivot->id,
                'stock' => $product->stock // Add stock information
            ];
        }

        $total = $cart->getTotal();

        $hasOrders = Order::where('user_id', Auth::id())->exists();

        return view('cart.show', compact('cartItems', 'total', 'hasOrders'));
    }

    public function remove($id)
    {
        $cart = $this->getOrCreateCart();

        // Find and delete the pivot record
        DB::table('cart_product')
            ->where('id', $id)
            ->where('cart_id', $cart->id)
            ->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang');
    }

    public function update(Request $request, $id)
    {
        $cartProduct = DB::table('cart_product')->where('id', $id)->first();

        if (!$cartProduct) {
            return redirect()->back()->with('error', 'Item tidak ditemukan di keranjang');
        }

        // Get the product to check its stock
        $product = Product::findOrFail($cartProduct->product_id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        $cart = $this->getOrCreateCart();

        // Find and update the pivot record
        DB::table('cart_product')
            ->where('id', $id)
            ->where('cart_id', $cart->id)
            ->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui');
    }

    private function getOrCreateCart()
    {
        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->id]);
        }
        return $cart;
    }
}
