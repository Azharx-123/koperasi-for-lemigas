<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotPurchasableException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\GuestCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $items = $this->getCartItems();

        if (empty($items)) {
            return redirect()->route('cart.show')->with('error', 'Keranjang belanja Anda kosong');
        }

        $cartItems = [];

        foreach ($items as $item) {
            $cartItems[$item['product']->id] = [
                'id' => $item['product']->id,
                'name' => $item['product']->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'image' => $item['product']->image,
                'pivot_id' => $item['product']->id
            ];
        }

        $total = $this->cartTotal($items);

        // Calculate shipping fee (could be dynamic based on address)
        $shipping_fee = Order::SHIPPING_FEE;

        // Calculate tax
        $tax = $total * Order::TAX_RATE;

        $profile = $this->profileForPrefill();

        return view('checkout.index', compact('cartItems', 'total', 'shipping_fee', 'tax', 'profile'));
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
            // #12: QRIS & E-Wallet ditambahkan sebagai simulasi (lihat blok
            // payment_status di bawah) — tidak ada payment gateway sungguhan
            // yang terpasang, jadi keduanya "lunas" seketika saat order
            // dibuat. Kartu Kredit tetap TIDAK ditawarkan: itu perlu SDK
            // payment gateway sungguhan (tokenisasi kartu, 3DS, dll) yang di
            // luar cakupan simulasi ini, beda dari QRIS/E-Wallet yang bisa
            // disimulasikan dengan jujur sebagai "langsung lunas".
            'payment_method' => 'required|in:' . implode(',', array_keys(Order::PAYMENT_METHODS)),
        ]);

        // Get cart items — DB kalau login, session (GuestCart) kalau tamu.
        $items = $this->getCartItems();

        if (empty($items)) {
            return redirect()->route('cart.show')->with('error', 'Keranjang belanja Anda kosong');
        }

        // Calculate totals
        $subtotal = $this->cartTotal($items);
        $shipping_fee = Order::SHIPPING_FEE;
        $tax = $subtotal * Order::TAX_RATE;
        $total = $subtotal + $shipping_fee + $tax;

        // #12: QRIS & E-Wallet disimulasikan lunas SAAT ITU JUGA — tidak ada
        // payment gateway sungguhan yang terpasang buat menunggu
        // webhook/callback konfirmasi pembayaran. Beda dari bank_transfer
        // yang tetap 'pending' sampai pembeli upload bukti transfer & admin
        // verifikasi manual lewat orders.payment.confirmation (lihat
        // OrderController & orders/show.blade.php — alur itu tidak disentuh,
        // tetap khusus bank_transfer).
        $payment_status = in_array($request->payment_method, ['qris', 'ewallet'], true)
            ? 'paid'
            : 'pending';

        try {
            // Begin transaction
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . Str::random(6),
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
                'payment_status' => $payment_status
            ]);

            // Create order items & deduct stock atomically. withoutAutoSync()
            // because this loop already deducts stock (with a race-safe
            // WHERE stock >= quantity guard) and the order's totals were
            // already set above from the cart — OrderItem's usual
            // auto-sync hooks (for admin-side edits) would otherwise
            // deduct stock a second time here.
            OrderItem::withoutAutoSync(function () use ($items, $order) {
                foreach ($items as $item) {
                    $product = $item['product'];
                    $quantity = $item['quantity'];

                    if (!$product->isPurchasable()) {
                        throw new ProductNotPurchasableException(
                            "Produk \"{$product->name}\" sudah tidak tersedia untuk dibeli. Silakan hapus dari keranjang."
                        );
                    }

                    // Single atomic UPDATE ... WHERE stock >= quantity.
                    // If two checkouts race for the last unit, only one of these
                    // succeeds — the second sees $deducted === 0 and rolls back.
                    $deducted = Product::where('id', $product->id)
                        ->where('stock', '>=', $quantity)
                        ->decrement('stock', $quantity);

                    if (!$deducted) {
                        $remaining = Product::where('id', $product->id)->value('stock') ?? 0;
                        throw new InsufficientStockException(
                            "Stok produk \"{$product->name}\" tidak mencukupi. Sisa stok: {$remaining}."
                        );
                    }

                    Product::where('id', $product->id)->increment('sold_count', $quantity);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $item['price'],
                        'total' => $quantity * $item['price']
                    ]);
                }
            });

            // Clear cart — DB kalau login, session (GuestCart) kalau tamu.
            if (Auth::check()) {
                $this->getCart()->products()->detach();
            } else {
                app(GuestCart::class)->clear();

                // #10: tamu tidak punya akun untuk "memiliki" order ini lewat
                // relasi user_id (order di atas dibuat dengan user_id null) —
                // jadi kepemilikan SESI INI atas order yang baru dibuat
                // dicatat di session, dipakai OrderPolicy (lihat
                // OrderPolicy::owns()) supaya sesi tamu ini — dan HANYA sesi
                // ini — bisa buka checkout.complete, orders.show, dan
                // ajukan/lihat konfirmasi pembayarannya sendiri tanpa login.
                session()->push('guest_order_ids', $order->id);
            }

            // Commit transaction
            DB::commit();

            // Redirect to thank you page or payment process
            return redirect()->route('checkout.complete', $order->id)->with('success', 'Pesanan berhasil dibuat!');
        } catch (InsufficientStockException | ProductNotPurchasableException $e) {
            // Rollback transaction — nothing was actually deducted or created
            DB::rollback();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
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

        // Ownership lewat OrderPolicy (bukan cek inline lagi) — sejak #10
        // route ini bisa diakses tamu, dan OrderPolicy::owns() sudah tahu
        // cara membedakan "order tamu milik sesi ini" dari "order tamu milik
        // orang lain" lewat session('guest_order_ids'). Cek inline yang lama
        // ($order->user_id !== Auth::id()) punya lubang keamanan begitu
        // route ini dibuka untuk tamu: untuk order tamu, user_id-nya null,
        // dan Auth::id() tamu juga null — null !== null itu false, jadi
        // SIAPA PUN yang belum login bisa buka checkout/complete/{order}
        // order tamu SIAPA SAJA cukup dengan menebak/menaikkan id di URL.
        $this->authorize('view', $order);

        return view('checkout.complete', compact('order'));
    }

    /**
     * Item keranjang, dinormalisasi ke bentuk yang sama terlepas dari
     * sumbernya (Cart DB kalau login, GuestCart/session kalau tamu — lihat
     * #10 di CATATAN-LANJUTAN-REVISI.md). index() dan process() sama-sama
     * pakai ini supaya keduanya tidak perlu peduli lagi mana sumbernya.
     *
     * @return array<int, array{product: Product, quantity: int, price: float}>
     */
    private function getCartItems(): array
    {
        if (Auth::check()) {
            $cart = $this->getCart();

            if (!$cart) {
                return [];
            }

            $items = [];

            foreach ($cart->products as $product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $product->pivot->quantity,
                    'price' => (float) $product->pivot->price,
                ];
            }

            return $items;
        }

        $items = [];

        foreach (app(GuestCart::class)->items() as $entry) {
            $product = Product::find($entry['id']);

            // GuestCart::items() sendiri sudah menyaring produk yang sudah
            // tidak ada — ini cuma jaga-jaga (defensive), harusnya tidak
            // pernah benar-benar kejadian.
            if (!$product) {
                continue;
            }

            $items[] = [
                'product' => $product,
                'quantity' => $entry['quantity'],
                'price' => $entry['price'],
            ];
        }

        return $items;
    }

    private function cartTotal(array $items): float
    {
        return array_sum(array_map(
            fn (array $item) => $item['price'] * $item['quantity'],
            $items
        ));
    }

    /**
     * #11: kalau user login & pernah isi data pengiriman di profil, form
     * checkout di-prefill dari situ dan ditawarkan checkbox "gunakan data
     * profil saya" (lihat checkout/index.blade.php) — supaya user yang
     * sudah pernah checkout tidak perlu ngetik ulang tiap kali. Tamu, dan
     * user yang profilnya masih kosong, tetap dapat form kosong seperti
     * biasa (return null).
     */
    private function profileForPrefill(): ?array
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        if (!$user->phone && !$user->address) {
            return null;
        }

        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'address' => $user->address,
            'province' => $user->province,
            'city' => $user->city,
            'district' => $user->district,
            'postal_code' => $user->postal_code,
        ];
    }

    private function getCart()
    {
        $user = Auth::user();
        return $user->cart;
    }
}
