<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Otorisasi akses panel admin ditangani oleh User::canAccessPanel()
        // (lihat app/Models/User.php), jadi tidak perlu didaftarkan ulang di
        // sini. Catatan: Filament::authorizeAccessUsing() tidak tersedia di
        // Filament v3.3.0 — jangan dipanggil di sini, karena akan
        // menyebabkan fatal error pada semua request /admin/*, termasuk
        // halaman login.
        Paginator::useBootstrapFive();
    }
}
