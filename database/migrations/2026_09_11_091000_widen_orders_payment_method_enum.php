<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * #12: QRIS & E-Wallet ditambahkan sebagai metode pembayaran simulasi
     * (lihat CheckoutController::process()) — keduanya langsung berstatus
     * 'paid' saat order dibuat, karena tidak ada payment gateway sungguhan
     * yang terpasang untuk menunggu callback/webhook konfirmasi. Kartu
     * Kredit ('credit_card') SENGAJA tidak ikut dikembalikan — itu perlu
     * integrasi gateway sungguhan (tokenisasi kartu, 3DS, dll.) yang di
     * luar cakupan simulasi ini, beda dari QRIS/E-Wallet yang bisa
     * disimulasikan dengan jujur sebagai "langsung lunas". Lihat juga
     * 2026_08_26_090000_narrow_orders_payment_method_enum.php, migrasi yang
     * dulu justru mempersempit kolom ini ke bank_transfer saja.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['bank_transfer', 'qris', 'ewallet'])
                ->default('bank_transfer')
                ->change();
        });
    }

    public function down(): void
    {
        // Sama seperti alasan di narrow_orders_payment_method_enum: fold
        // dulu baris qris/ewallet yang mungkin sudah tersimpan sebelum
        // mempersempit lagi ke bank_transfer saja, supaya rollback ini
        // aman dijalankan di database mana pun (bukan cuma database yang
        // masih kosong).
        DB::table('orders')
            ->whereNotIn('payment_method', ['bank_transfer'])
            ->update(['payment_method' => 'bank_transfer']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['bank_transfer'])
                ->default('bank_transfer')
                ->change();
        });
    }
};
