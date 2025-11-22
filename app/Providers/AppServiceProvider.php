<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Konfigurasi otentikasi dan otorisasi
        Filament::serving(function () {
            // Batasi akses hanya untuk admin
            Filament::authorizeAccessUsing(function () {
                return auth::check() && auth::user()->role === 'admin';
            });
        });
    }
}