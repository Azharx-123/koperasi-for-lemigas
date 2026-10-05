<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed a small, coherent product catalog so the storefront (product
     * grid, homepage "Produk Unggulan", search) has something real to
     * display out of the box instead of an empty state.
     *
     * Only reuses image files already shipped in storage/app/public/products
     * that are generic product photography with no third-party seller
     * branding, marketplace screenshots, or real names on them — see the
     * portfolio clean-up notes in this project's README for why the other
     * originally-uploaded product photos were removed instead of reused.
     *
     * Depends on CategorySeeder having run first. updateOrCreate() on 'slug'
     * keeps this idempotent and safe to re-run.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'slug');

        $products = [
            [
                'name' => 'Genset Bensin 2800 Watt',
                'slug' => 'genset-bensin-2800-watt',
                'short_description' => 'Genset portabel untuk daya cadangan di lokasi kerja dan area operasional.',
                'description' => 'Genset berbahan bakar bensin dengan daya 2800 watt, cocok sebagai sumber '
                    . 'daya cadangan untuk kebutuhan operasional lapangan, posko tambang, maupun kantor unit '
                    . 'anggota. Dilengkapi rangka portabel dan panel indikator tegangan/arus.',
                'price' => 8500000,
                'image' => 'products/01JNAVZKHBV6Q8HP7Q7DQK292C.webp',
                'sold_count' => 42,
                'stock' => 15,
                'category_id' => $categoryIds['genset-kelistrikan'],
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Detektor Gas Portable',
                'slug' => 'detektor-gas-portable',
                'short_description' => 'Alat deteksi kebocoran gas portabel untuk mendukung keselamatan kerja.',
                'description' => 'Detektor gas genggam dengan indikator level dan alarm, dirancang untuk '
                    . 'pemeriksaan rutin area tambang, gudang bahan bakar, dan instalasi energi anggota koperasi. '
                    . 'Ringan dan mudah dibawa saat inspeksi lapangan.',
                'price' => 1250000,
                'image' => 'products/01JNASFMHSMD7ZRZG69TZ45787.webp',
                'sold_count' => 67,
                'stock' => 30,
                'category_id' => $categoryIds['alat-keselamatan-kerja-k3'],
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Console Server Manajemen Jaringan',
                'slug' => 'console-server-manajemen-jaringan',
                'short_description' => 'Perangkat manajemen akses jarak jauh untuk infrastruktur jaringan lapangan.',
                'description' => 'Console server rak untuk manajemen dan akses jarak jauh ke perangkat jaringan '
                    . 'di lokasi operasional yang tersebar, membantu tim teknis memantau infrastruktur tanpa '
                    . 'harus selalu hadir di lokasi.',
                'price' => 8200000,
                'image' => 'products/01JNATPWZX6C0CTMQC36NF8XHM.webp',
                'sold_count' => 9,
                'stock' => 6,
                'category_id' => $categoryIds['instrumentasi-jaringan'],
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Modul Kontroler Elektronik',
                'slug' => 'modul-kontroler-elektronik',
                'short_description' => 'Modul elektronik untuk kebutuhan instrumentasi dan otomasi sederhana.',
                'description' => 'Modul kontroler serbaguna yang dapat dipakai untuk proyek instrumentasi dan '
                    . 'otomasi skala kecil di lingkungan bengkel maupun laboratorium teknis anggota.',
                'price' => 385000,
                'image' => 'products/01JNARQY5Q7D8SR9AZ40JFWWR3.jpg',
                'sold_count' => 24,
                'stock' => 50,
                'category_id' => $categoryIds['instrumentasi-jaringan'],
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Laptop Gaming Series',
                'slug' => 'laptop-gaming-series',
                'short_description' => 'Laptop performa tinggi untuk pengolahan data dan desain teknis.',
                'description' => 'Laptop dengan performa tinggi, cocok untuk anggota yang membutuhkan daya '
                    . 'komputasi lebih untuk pengolahan data, pemodelan, maupun desain teknis pekerjaan '
                    . 'sehari-hari.',
                'price' => 12500000,
                'image' => 'products/01JM757EQ1ZWMEJQQME16DWT7Y.jpg',
                'sold_count' => 13,
                'stock' => 8,
                'category_id' => $categoryIds['elektronik-komputer'],
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Laptop Ringan Serbaguna',
                'slug' => 'laptop-ringan-serbaguna',
                'short_description' => 'Laptop ringan untuk kebutuhan administrasi dan operasional kantor.',
                'description' => 'Laptop ringan dan hemat daya, cocok untuk kebutuhan administrasi harian, '
                    . 'input data, dan operasional kantor unit-unit usaha koperasi.',
                'price' => 6800000,
                'image' => 'products/01JMA9VF6NN2SG0YT4J9VRZYFR.webp',
                'sold_count' => 31,
                'stock' => 20,
                'category_id' => $categoryIds['elektronik-komputer'],
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Laptop Tipis Mobilitas Tinggi',
                'slug' => 'laptop-tipis-mobilitas-tinggi',
                'short_description' => 'Laptop tipis dan ringan, cocok untuk staf dengan mobilitas tinggi.',
                'description' => 'Desain tipis dan bobot ringan membuat laptop ini nyaman dibawa bepergian, '
                    . 'cocok untuk staf lapangan maupun kantor yang sering berpindah lokasi kerja.',
                'price' => 7400000,
                'image' => 'products/01JMAATDB832D5GXJE2HVCM63X.webp',
                'sold_count' => 18,
                'stock' => 12,
                'category_id' => $categoryIds['elektronik-komputer'],
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Mixer Adonan Stainless Kapasitas Besar',
                'slug' => 'mixer-adonan-stainless-kapasitas-besar',
                'short_description' => 'Peralatan penunjang unit usaha katering/konsumsi anggota koperasi.',
                'description' => 'Mixer adonan berbahan stainless steel dengan kapasitas besar, ditujukan untuk '
                    . 'unit usaha konsumsi/katering yang dikelola anggota koperasi.',
                'price' => 4200000,
                'image' => 'products/01JNAT4PCE58G3P6S3P9QBPZB2.webp',
                'sold_count' => 5,
                'stock' => 4,
                'category_id' => $categoryIds['peralatan-usaha-penunjang'],
                'status' => 'active',
                'featured' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
