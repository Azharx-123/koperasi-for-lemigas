<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lokal saja: arahkan semua request ke SATU host resmi (dari APP_URL),
 * bukan membiarkan 127.0.0.1 dan localhost dipakai bergantian.
 *
 * Browser memperlakukan 127.0.0.1 dan localhost sebagai origin yang
 * BERBEDA sekalipun sama-sama menuju mesin yang sama - cookie session
 * (jadi juga login dan keranjang belanja tamu) tidak pernah dibagi antar
 * keduanya. Efeknya: login/isi keranjang di satu alamat lalu buka alamat
 * satunya terasa seperti "tiba-tiba logout" atau "keranjang jadi kosong",
 * padahal itu memang dua sesi yang sepenuhnya terpisah. Vite dev server
 * project ini juga sudah dikunci ke 127.0.0.1 (lihat vite.config.js dan
 * SecurityHeaders::handle()), jadi mengakses lewat localhost bisa membuat
 * asset dev/HMR gagal termuat karena origin-nya tidak cocok.
 *
 * Middleware ini menyatukan semuanya dengan redirect otomatis ke host yang
 * dikonfigurasi di APP_URL, hanya untuk alias localhost/127.0.0.1 yang
 * dikenal - request dari alamat lain (mis. IP LAN saat diakses dari HP di
 * jaringan yang sama) dibiarkan apa adanya.
 *
 * TIDAK PERNAH aktif di luar environment 'local': di production, APP_URL
 * memang domain aslinya, jadi redirect di sini tidak perlu (request sudah
 * di host yang benar) dan berbahaya kalau APP_URL sampai salah diisi.
 */
class CanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('local')) {
            return $next($request);
        }

        $canonical = parse_url((string) config('app.url'));

        if (! $canonical || empty($canonical['host'])) {
            return $next($request);
        }

        $canonicalHost = $canonical['host'];
        $currentHost = $request->getHost();

        if (strcasecmp($currentHost, $canonicalHost) === 0) {
            return $next($request);
        }

        // Cuma turun tangan untuk alias "mesin yang sama, nama beda" yang
        // sudah dikenal - bukan untuk host asing (mis. diakses lewat IP LAN
        // dari perangkat lain), yang harus tetap berjalan normal.
        $knownAliases = ['localhost', '127.0.0.1', '[::1]', '::1'];

        if (! in_array(strtolower($currentHost), $knownAliases, true)) {
            return $next($request);
        }

        $scheme = $canonical['scheme'] ?? $request->getScheme();
        $port = $canonical['port'] ?? $request->getPort();
        $portSuffix = in_array((int) $port, [80, 443], true) ? '' : ':' . $port;

        $target = $scheme . '://' . $canonicalHost . $portSuffix . $request->getRequestUri();

        return redirect()->to($target);
    }
}
