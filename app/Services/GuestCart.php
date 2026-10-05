<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

/**
 * Keranjang belanja untuk pengunjung yang belum login. Disimpan di session
 * (key 'guest_cart'), BUKAN sebagai baris baru di tabel `carts` — supaya
 * tidak perlu migrasi skema carts.user_id jadi nullable, tidak perlu
 * strategi cleanup keranjang tamu yang ditinggal begitu saja, dan lebih
 * gampang di-merge ke akun asli saat login/register (lihat
 * mergeIntoUserCart()).
 *
 * Bentuk data session: [product_id => ['quantity' => int, 'price' => float]]
 * — 'price' adalah snapshot harga saat produk ditambahkan, persis seperti
 * kolom cart_product.price untuk keranjang milik user yang login.
 *
 * items() SENGAJA mengembalikan array dengan shape yang PERSIS SAMA seperti
 * yang sudah dipakai $cartItems di cart/show.blade.php dan
 * checkout/index.blade.php (keyed by product_id, yang juga dipakai sebagai
 * 'pivot_id') — supaya kedua view itu tidak perlu tahu apakah yang sedang
 * ditampilkan itu keranjang tamu atau keranjang DB milik user.
 */
class GuestCart
{
    private const SESSION_KEY = 'guest_cart';

    /**
     * Tambah produk ke keranjang tamu. Asumsi: pemanggil (CartController)
     * sudah validasi quantity dasar (required|integer|min:1|max:stock)
     * sebelum sampai sini — method ini fokus ke aturan yang butuh tahu isi
     * keranjang saat ini: kombinasi quantity lama+baru tidak boleh melebihi
     * stok, sama seperti cabang DB-cart di CartController::add().
     */
    public function add(Product $product, int $quantity): array
    {
        if (!$product->isPurchasable()) {
            return ['success' => false, 'message' => 'Produk ini sedang tidak tersedia untuk dibeli.'];
        }

        $cart = $this->getCartArray();
        $existingQuantity = $cart[$product->id]['quantity'] ?? 0;
        $newQuantity = $existingQuantity + $quantity;

        if ($newQuantity > $product->stock) {
            $message = $existingQuantity > 0
                ? "Jumlah di keranjang ({$existingQuantity}) + jumlah baru ({$quantity}) melebihi stok tersedia ({$product->stock})."
                : "Jumlah melebihi stok tersedia ({$product->stock}).";

            return ['success' => false, 'message' => $message];
        }

        // Price snapshot di-refresh ke harga terkini setiap kali nambah —
        // sama seperti CartController::add(): pelanggan yang menambah
        // quantity produk yang sudah ada di keranjang tidak boleh terus
        // bayar harga lama, meskipun masih dalam sesi tamu yang sama.
        $cart[$product->id] = [
            'quantity' => $newQuantity,
            'price' => (float) $product->price,
        ];

        $this->putCartArray($cart);

        return ['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang'];
    }

    /**
     * Ubah quantity satu item. $productId di sini adalah 'pivot_id' yang
     * dilihat blade — untuk keranjang tamu keduanya memang sama (lihat
     * catatan shape di atas class). Return array sengaja menyertakan
     * 'status' (kode HTTP) supaya pemanggil (CartController::update()) bisa
     * langsung dipakai untuk response JSON tanpa perlu menebak-nebak dari
     * isi teks 'message' (rapuh kalau pesannya berubah nanti).
     */
    public function update(int $productId, int $quantity): array
    {
        $cart = $this->getCartArray();

        if (!isset($cart[$productId])) {
            return ['success' => false, 'status' => 404, 'message' => 'Item tidak ditemukan di keranjang'];
        }

        $product = Product::find($productId);

        if (!$product) {
            // Produk sudah tidak ada (dihapus/di-nonaktifkan) sejak
            // ditambahkan — tidak ada quantity valid yang bisa disimpan,
            // sekalian buang dari keranjang tamu.
            unset($cart[$productId]);
            $this->putCartArray($cart);

            return ['success' => false, 'status' => 404, 'message' => 'Produk ini sudah tidak tersedia.'];
        }

        if ($quantity < 1 || $quantity > $product->stock) {
            return ['success' => false, 'status' => 422, 'message' => "Jumlah harus antara 1 dan stok tersedia ({$product->stock})."];
        }

        $cart[$productId]['quantity'] = $quantity;
        $this->putCartArray($cart);

        return ['success' => true, 'status' => 200, 'message' => 'Keranjang berhasil diperbarui'];
    }

    public function remove(int $productId): void
    {
        $cart = $this->getCartArray();
        unset($cart[$productId]);
        $this->putCartArray($cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Shape PERSIS sama seperti $cartItems yang sudah dipakai
     * cart/show.blade.php & checkout/index.blade.php untuk keranjang DB.
     * Produk yang sudah dihapus/di-soft-delete sejak ditambahkan otomatis
     * tidak ikut ditampilkan (Product::whereIn() otomatis exclude baris
     * soft-deleted) — sama seperti perilaku keranjang DB yang sudah ada:
     * pivot row yang product-nya sudah di-soft-delete juga otomatis tidak
     * muncul lewat relasi $cart->products().
     */
    public function items(): array
    {
        $cart = $this->getCartArray();

        if (empty($cart)) {
            return [];
        }

        // Satu query batch, bukan Product::find() per baris.
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        $items = [];

        foreach ($cart as $productId => $entry) {
            $product = $products->get($productId);

            if (!$product) {
                continue;
            }

            $items[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $entry['price'],
                'quantity' => $entry['quantity'],
                'image' => $product->image,
                'pivot_id' => $productId,
                'stock' => $product->stock,
            ];
        }

        return $items;
    }

    /**
     * count()/total() dihitung dari items() (bukan langsung dari session)
     * supaya konsisten dengan filter produk-yang-sudah-hilang di atas —
     * item yang tidak lagi valid tidak ikut dihitung di badge cart maupun
     * total belanja.
     */
    public function count(): int
    {
        return array_sum(array_column($this->items(), 'quantity'));
    }

    public function total(): float
    {
        return array_sum(array_map(
            fn (array $item) => $item['price'] * $item['quantity'],
            $this->items()
        ));
    }

    /**
     * Dipanggil sekali tepat setelah login/register sukses (lihat
     * LoginController::login() & RegisteredUserController::store()) —
     * pindahkan isi keranjang tamu ke Cart DB milik user, pakai aturan
     * combine+cap-di-stock yang sama seperti CartController::add()
     * (termasuk penanganan race unique-constraint-nya), lalu kosongkan
     * session guest cart.
     */
    public function mergeIntoUserCart(User $user): void
    {
        $guestItems = $this->getCartArray();

        if (empty($guestItems)) {
            return;
        }

        $cart = $user->cart;

        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->id]);
        }

        foreach ($guestItems as $productId => $entry) {
            $product = Product::find($productId);

            // Produk sudah tidak valid lagi — tidak ada yang bisa digabung.
            if (!$product) {
                continue;
            }

            $existingProduct = $cart->products()->where('product_id', $productId)->first();

            if ($existingProduct) {
                $newQuantity = min(
                    $existingProduct->pivot->quantity + $entry['quantity'],
                    $product->stock
                );

                $cart->products()->updateExistingPivot($productId, [
                    'quantity' => $newQuantity,
                    'price' => $product->price,
                ]);
            } else {
                try {
                    $cart->products()->attach($productId, [
                        'quantity' => min($entry['quantity'], $product->stock),
                        'price' => $product->price,
                    ]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // Race yang sama seperti CartController::add(): baris
                    // pivot-nya sudah dibuat request lain di antara cek dan
                    // attach ini (mis. tab lain yang juga sedang merge).
                    $existingProduct = $cart->products()->where('product_id', $productId)->first();
                    $newQuantity = min(
                        $existingProduct->pivot->quantity + $entry['quantity'],
                        $product->stock
                    );

                    $cart->products()->updateExistingPivot($productId, [
                        'quantity' => $newQuantity,
                        'price' => $product->price,
                    ]);
                }
            }
        }

        $this->clear();
    }

    private function getCartArray(): array
    {
        return session(self::SESSION_KEY, []);
    }

    private function putCartArray(array $cart): void
    {
        session([self::SESSION_KEY => $cart]);
    }
}
