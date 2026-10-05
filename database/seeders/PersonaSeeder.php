<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    /**
     * Seed the "Siapa yang Cocok dengan Produk Kami?" persona cards on the
     * homepage. `count` drives the animated counter in welcome.blade.php's
     * initCounters() — kept as round, clearly-illustrative numbers rather
     * than anything meant to look like an audited membership figure.
     *
     * updateOrCreate() on 'title' keeps this idempotent and safe to re-run.
     */
    public function run(): void
    {
        $personas = [
            [
                'title' => 'Pelaku Usaha Tambang',
                'subtitle' => 'Kebutuhan alat & keselamatan kerja',
                'description' => 'Menyediakan peralatan penunjang operasional dan keselamatan kerja bagi '
                    . 'pelaku usaha di sektor pertambangan.',
                'icon' => 'fas fa-mountain',
                'count' => 150,
                'order' => 1,
            ],
            [
                'title' => 'Teknisi & Kontraktor Energi',
                'subtitle' => 'Perangkat teknis dan instrumentasi',
                'description' => 'Mendukung kebutuhan perangkat teknis dan instrumentasi bagi teknisi maupun '
                    . 'kontraktor di sektor energi.',
                'icon' => 'fas fa-wrench',
                'count' => 120,
                'order' => 2,
            ],
            [
                'title' => 'Anggota Koperasi',
                'subtitle' => 'Layanan simpan pinjam & ritel',
                'description' => 'Memberikan akses layanan simpan pinjam dan kebutuhan ritel sehari-hari '
                    . 'khusus untuk anggota koperasi.',
                'icon' => 'fas fa-handshake',
                'count' => 500,
                'order' => 3,
            ],
        ];

        foreach ($personas as $persona) {
            Persona::updateOrCreate(['title' => $persona['title']], $persona);
        }
    }
}
