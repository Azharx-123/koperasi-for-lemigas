<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Services\GuestCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if (!$product->isPurchasable()) {
            $message = 'Produk ini sedang tidak tersedia untuk dibeli.';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        // Web bisa dipakai anonim (lihat #10 di CATATAN-LANJUTAN-REVISI.md):
        // pengunjung yang belum login dapat GuestCart (session), bukan Cart
        // DB. GuestCart::add() menangani aturan "kombinasi quantity lama+baru
        // tidak boleh melebihi stok" sendiri — persis logika yang sama
        // seperti cabang DB di bawah, cuma sumber datanya beda.
        if (!Auth::check()) {
            $guestCart = app(GuestCart::class);
            $result = $guestCart->add($product, (int) $request->quantity);

            if (!$result['success']) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $result['message']], 422);
                }

                return redirect()->back()->with('error', $result['message']);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'cartCount' => $guestCart->count(),
                    'cartTotal' => $guestCart->total(),
                ]);
            }

            return redirect()->back()->with('success', $result['message']);
        }

        // Get or create user's cart
        $cart = $this->getOrCreateCart();

        // Check if product already exists in cart
        $existingProduct = $cart->products()->where('product_id', $product->id)->first();

        if ($existingProduct) {
            // Combined quantity must not exceed available stock
            $newQuantity = $existingProduct->pivot->quantity + $request->quantity;

            if ($newQuantity > $product->stock) {
                $message = "Jumlah di keranjang ({$existingProduct->pivot->quantity}) + jumlah baru ({$request->quantity}) melebihi stok tersedia ({$product->stock}).";

                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->with('error', $message);
            }

            // Update quantity AND price — CheckoutController snapshots
            // OrderItem::price from $product->pivot->price at checkout time,
            // so price needs to stay current here too: otherwise a customer
            // who bumps quantity on an item already in their cart would keep
            // paying whatever the price was when they first added it, even
            // after an admin changes it.
            $cart->products()->updateExistingPivot($product->id, [
                'quantity' => $newQuantity,
                'price' => $product->price,
            ]);
        } else {
            try {
                // Add new product to cart
                $cart->products()->attach($product->id, [
                    'quantity' => $request->quantity,
                    'price' => $product->price
                ]);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // The existence check above and this attach() aren't atomic,
                // so a double-click (or a retried request) can race past the
                // check twice before either commits. The unique(cart_id,
                // product_id) constraint on the cart_product table turns
                // that race into this exception instead of a silent
                // duplicate row — fold this request's quantity into the row
                // that won the race.
                $existingProduct = $cart->products()->where('product_id', $product->id)->first();
                $newQuantity = min(
                    $existingProduct->pivot->quantity + $request->quantity,
                    $product->stock
                );

                $cart->products()->updateExistingPivot($product->id, [
                    'quantity' => $newQuantity,
                    'price' => $product->price,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cartCount' => $cart->getItemCount(),
                'cartTotal' => $cart->getTotal()
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function show()
    {
        if (Auth::check()) {
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

            // Auth::id() tidak pernah null di cabang ini, jadi aman langsung
            // dipakai (lihat cabang guest di bawah untuk alasan kenapa versi
            // itu tidak bisa pakai pola yang sama).
            $hasOrders = Order::where('user_id', Auth::id())->exists();
        } else {
            $guestCart = app(GuestCart::class);
            $cartItems = $guestCart->items();
            $total = $guestCart->total();

            // Riwayat pesanan memang fitur khusus akun (lihat orders.index
            // yang tetap di belakang middleware auth) — tamu tidak punya
            // riwayat untuk dicek di sini.
            $hasOrders = false;
        }

        return view('cart.show', compact('cartItems', 'total', 'hasOrders'));
    }

    public function remove($id)
    {
        if (!Auth::check()) {
            app(GuestCart::class)->remove((int) $id);

            return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang');
        }

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
        // #4/#9: tombol update per-baris sudah dihapus dari cart/show.blade.php
        // — checkout-btn di sana sekarang submit perubahan lewat fetch()
        // AJAX sebelum redirect ke checkout, makanya method ini perlu sadar
        // AJAX (response JSON), bukan cuma redirect()->back() seperti
        // sebelumnya. Fallback redirect tetap dipertahankan untuk request
        // non-AJAX (mis. kalau JS gagal load, href asli checkout-btn masih
        // berfungsi sebagai link biasa tanpa submit perubahan).
        if (!Auth::check()) {
            $result = app(GuestCart::class)->update((int) $id, (int) $request->input('quantity'));

            if (!$result['success']) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $result['message']], $result['status']);
                }

                return redirect()->back()->with('error', $result['message']);
            }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $result['message']]);
            }

            return redirect()->back()->with('success', $result['message']);
        }

        $cart = $this->getOrCreateCart();

        // Scope the lookup to this user's cart so a crafted $id
        // from another user's cart can't be read or updated
        $cartProduct = DB::table('cart_product')
            ->where('id', $id)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$cartProduct) {
            $message = 'Item tidak ditemukan di keranjang';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }

            return redirect()->back()->with('error', $message);
        }

        // Get the product to check its stock
        $product = Product::findOrFail($cartProduct->product_id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        // Find and update the pivot record
        DB::table('cart_product')
            ->where('id', $id)
            ->where('cart_id', $cart->id)
            ->update(['quantity' => $request->quantity]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Keranjang berhasil diperbarui']);
        }

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
