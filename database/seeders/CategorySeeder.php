<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the product categories used by ProductSeeder.
     *
     * Names reflect KEP's two business lines from CompanySeeder's
     * description (energy/mining focus + a general retail unit), so the
     * catalog reads as one coherent cooperative rather than a generic shop.
     * updateOrCreate() on 'slug' keeps this idempotent and safe to re-run.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Genset & Kelistrikan',
                'slug' => 'genset-kelistrikan',
                'description' => 'Genset dan perlengkapan kelistrikan untuk kebutuhan daya di lokasi operasional.',
            ],
            [
                'name' => 'Alat Keselamatan Kerja (K3)',
                'slug' => 'alat-keselamatan-kerja-k3',
                'description' => 'Peralatan keselamatan dan deteksi bahaya untuk mendukung K3 di area tambang dan energi.',
            ],
            [
                'name' => 'Instrumentasi & Jaringan',
                'slug' => 'instrumentasi-jaringan',
                'description' => 'Perangkat instrumentasi, kontrol, dan jaringan untuk kebutuhan teknis lapangan.',
            ],
            [
                'name' => 'Elektronik & Komputer',
                'slug' => 'elektronik-komputer',
                'description' => 'Laptop dan perangkat elektronik untuk kebutuhan kantor dan operasional anggota.',
            ],
            [
                'name' => 'Peralatan Usaha & Penunjang',
                'slug' => 'peralatan-usaha-penunjang',
                'description' => 'Peralatan penunjang unit usaha ritel dan layanan pendukung anggota koperasi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
