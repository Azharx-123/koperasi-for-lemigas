<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Seed the "Mengapa Memilih Kami?" / KEP-description feature list on
     * the homepage (see WelcomeController + welcome.blade.php, which reuses
     * this same active/ordered collection in both sections).
     *
     * Icons are Font Awesome 5 Free solid classes (bundled via
     * resources/css/app.css) to match how welcome.blade.php renders them:
     * `<i class="{{ $feature->icon }} fa-2x text-dark"></i>`.
     *
     * updateOrCreate() on 'title' keeps this idempotent and safe to re-run.
     */
    public function run(): void
    {
        $features = [
            [
                'title' => 'Transparan & Terpercaya',
                'description' => 'Pengelolaan keuangan koperasi dilakukan secara transparan dan dapat '
                    . 'dipertanggungjawabkan kepada seluruh anggota.',
                'icon' => 'fas fa-shield-alt',
                'order' => 1,
            ],
            [
                'title' => 'Berbasis Keanggotaan',
                'description' => 'Setiap keputusan usaha mengutamakan kesejahteraan dan kebutuhan anggota '
                    . 'koperasi.',
                'icon' => 'fas fa-users',
                'order' => 2,
            ],
            [
                'title' => 'Fokus Energi & Pertambangan',
                'description' => 'Produk dan layanan disesuaikan dengan kebutuhan nyata sektor energi dan '
                    . 'pertambangan.',
                'icon' => 'fas fa-hard-hat',
                'order' => 3,
            ],
            [
                'title' => 'Jangkauan Nasional',
                'description' => 'Melayani distribusi kebutuhan usaha anggota di berbagai wilayah Indonesia.',
                'icon' => 'fas fa-truck',
                'order' => 4,
            ],
            [
                'title' => 'Simpan Pinjam Fleksibel',
                'description' => 'Layanan simpan pinjam dengan skema yang disesuaikan kemampuan anggota.',
                'icon' => 'fas fa-coins',
                'order' => 5,
            ],
            [
                'title' => 'Layanan Purnajual',
                'description' => 'Dukungan layanan pelanggan untuk kebutuhan anggota setelah transaksi.',
                'icon' => 'fas fa-headset',
                'order' => 6,
            ],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(['title' => $feature['title']], $feature);
        }
    }
}
