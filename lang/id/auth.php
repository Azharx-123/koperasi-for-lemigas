<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Autentikasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut dipakai selama proses autentikasi untuk
    | berbagai pesan yang perlu ditampilkan ke pengguna. Aplikasi ini
    | menampilkan pesan login-gagalnya sendiri secara langsung dari
    | LoginController, jadi baris di bawah ini terutama untuk jaga-jaga
    | apabila ada bagian lain (mis. paket pihak ketiga) yang merujuk ke
    | kunci bahasa auth.* standar Laravel.
    |
    */

    'failed' => 'Email atau password salah.',
    'password' => 'Password yang dimasukkan salah.',
    'throttle' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.',

];
