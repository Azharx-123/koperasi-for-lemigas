<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    /**
     * Seed the "Partner & Sponsor Kami" logo strip on the homepage.
     *
     * These six names/logos are entirely fictional, purpose-made for this
     * portfolio (see storage/app/public/sponsors/*.svg) — the project
     * originally had real, trademarked brand logos wired up here (from
     * unrelated product-testing uploads), which have been removed since a
     * demo shouldn't imply a real company's sponsorship. website_url uses
     * the *.example.com reserved domain so the links resolve to nothing
     * rather than someone else's real site.
     *
     * updateOrCreate() on 'name' keeps this idempotent and safe to re-run.
     */
    public function run(): void
    {
        $sponsors = [
            [
                'name' => 'Nusantara Energi',
                'logo' => 'sponsors/sponsor-nusantara-energi.svg',
                'website_url' => 'https://nusantaraenergi.example.com',
                'order' => 1,
            ],
            [
                'name' => 'Mitra Tambang',
                'logo' => 'sponsors/sponsor-mitra-tambang.svg',
                'website_url' => 'https://mitratambang.example.com',
                'order' => 2,
            ],
            [
                'name' => 'Cipta Daya',
                'logo' => 'sponsors/sponsor-cipta-daya.svg',
                'website_url' => 'https://ciptadaya.example.com',
                'order' => 3,
            ],
            [
                'name' => 'Sentosa Mineral',
                'logo' => 'sponsors/sponsor-sentosa-mineral.svg',
                'website_url' => 'https://sentosamineral.example.com',
                'order' => 4,
            ],
            [
                'name' => 'Prima Teknik',
                'logo' => 'sponsors/sponsor-prima-teknik.svg',
                'website_url' => 'https://primateknik.example.com',
                'order' => 5,
            ],
            [
                'name' => 'Bumi Makmur',
                'logo' => 'sponsors/sponsor-bumi-makmur.svg',
                'website_url' => 'https://bumimakmur.example.com',
                'order' => 6,
            ],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::updateOrCreate(['name' => $sponsor['name']], $sponsor);
        }
    }
}
