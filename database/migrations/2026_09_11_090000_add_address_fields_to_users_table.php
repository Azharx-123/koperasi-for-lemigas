<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * #11: kolom alamat pengiriman di profil, dipakai untuk prefill form
     * checkout (lihat CheckoutController::profileForPrefill() dan checkbox
     * "gunakan data profil saya" di checkout/index.blade.php) — supaya user
     * yang sudah pernah checkout tidak perlu mengetik ulang no. HP & alamat
     * setiap kali belanja lagi. Semua nullable: user baru, atau yang belum
     * pernah checkout, belum tentu sudah mengisi ini.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('company')->nullable()->after('phone');
            $table->text('address')->nullable()->after('company');
            $table->string('province')->nullable()->after('address');
            $table->string('city')->nullable()->after('province');
            $table->string('district')->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'company', 'address', 'province', 'city', 'district', 'postal_code']);
        });
    }
};
