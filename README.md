# KEP — Koperasi Energi dan Pertambangan

Company profile sekaligus e-commerce sederhana untuk **Koperasi Energi dan
Pertambangan (KEP)** — sebuah koperasi **fiktif**, dibangun dengan Laravel
dan Filament sebagai proyek portofolio.

> **Catatan:** KEP beserta seluruh data di dalamnya (profil, sejarah, nomor
> legalitas, produk, mitra/sponsor, testimoni) adalah **fiktif** dan dibuat
> khusus untuk keperluan demo portofolio ini. Nomor rekening, alamat, dan
> kontak yang tampil bukan data nyata.
m prune
## Fitur

- **Halaman publik** — beranda dengan hero carousel, profil koperasi
  (visi/misi/sejarah), katalog & detail produk, keranjang, checkout dengan
  konfirmasi pembayaran manual, riwayat pesanan, dan ulasan produk.
- **Autentikasi** — registrasi/login (Laravel Breeze), verifikasi email, dan
  Two-Factor Authentication.
- **Panel admin (Filament)** — pengelolaan produk & kategori, pesanan,
  konfirmasi pembayaran, ulasan, carousel, fitur unggulan, persona
  target pengguna, sponsor/mitra, dan profil koperasi.

## Tumpukan Teknologi

- Laravel 11 (PHP 8.2+) · Filament 3
- Bootstrap 5, AOS, Font Awesome 5 — dibundel lewat Vite (bukan CDN)
- MySQL/MariaDB (atau driver lain yang didukung Laravel)

## Menjalankan di Lokal

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
# Sesuaikan koneksi database di .env, lalu:
php artisan migrate

# Password akun admin WAJIB diset eksplisit di luar local/testing;
# di local, boleh dikosongkan dan seeder akan memakai "password" sbg default.
php artisan db:seed

php artisan storage:link
npm run build   # atau `npm run dev` saat development
php artisan serve
```

Setelah seed, tersedia dua akun contoh: `test@example.com` (user biasa) dan
`admin@example.com` (role admin, password sesuai `ADMIN_SEED_PASSWORD` di
`.env`, atau `password` bila variabel itu tidak diset di environment local).

## Tentang Data Demo

Seeder (`database/seeders/`) mengisi:

- **CompanySeeder** — profil koperasi fiktif.
- **CategorySeeder** & **ProductSeeder** — 5 kategori dan 8 produk contoh,
  memakai foto produk generik (tanpa watermark toko/marketplace pihak lain).
- **FeatureSeeder**, **PersonaSeeder** — konten teks untuk section
  "Mengapa Memilih Kami" dan target pengguna di beranda.
- **CarouselItemSeeder** — 6 slide hero dengan foto stok bebas identitas.
- **SponsorSeeder** — 6 logo mitra, seluruhnya **nama & logo rekaan**
  (`storage/app/public/sponsors/*.svg`), bukan brand asli — supaya tidak
  terkesan diendorse perusahaan tertentu.

Bila menambah upload baru lewat panel admin saat development, ingat bahwa
`storage/app/public/.gitignore` sengaja hanya meng-whitelist file demo di
atas; file baru tidak otomatis ikut ter-commit ke git kecuali ditambahkan
manual ke whitelist tersebut.
