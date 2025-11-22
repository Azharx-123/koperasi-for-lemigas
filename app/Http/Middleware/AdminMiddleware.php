<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip pengecekan jika sedang di halaman login atau register
        if ($request->routeIs('filament.admin.auth.login') || $request->routeIs('filament.admin.auth.register')) {
            return $next($request);
        }

        $user = Filament::auth()->user();
        
        if (!$user || $user->role !== 'admin') {
            if ($user) {
                Filament::auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            
            return to_route('filament.admin.auth.login')
                ->with('error', 'Maaf, hanya admin yang diizinkan mengakses halaman ini.');
        }

        return $next($request);
    }
}