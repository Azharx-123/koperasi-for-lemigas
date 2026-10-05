<?php

namespace Database\Seeders;

use App\Models\CarouselItem;
use Illuminate\Database\Seeder;

class CarouselItemSeeder extends Seeder
{
    /**
     * Seed the homepage hero carousel. Reuses the generic, license-safe
     * stock photography already shipped in storage/app/public/carousel
     * (no identifiable people, no third-party logos) — see the portfolio
     * clean-up notes for why other originally-uploaded images were removed
     * instead of reused.
     *
     * updateOrCreate() on 'title' keeps this idempotent and safe to re-run.
     */
    public function run(): void
    {
        $items = [
            [
                'title' => 'Mendukung Sektor Energi Nasional',
                'subtitle' => 'Berkontribusi dalam rantai pasok kebutuhan sektor energi dan pertambangan.',
                'image' => 'carousel/01JMEA06Y048MT4X6K9HRWTXPD.jpg',
                'order' => 1,
            ],
            [
                'title' => 'Transisi Menuju Energi Berkelanjutan',
                'subtitle' => 'Turut mendukung pengembangan energi baru dan terbarukan.',
                'image' => 'carousel/01JKWG5H21NJ3Q06QMZ0WNQCWK.jpg',
                'order' => 2,
            ],
            [
                'title' => 'Komitmen Reklamasi & Kelestarian Lingkungan',
                'subtitle' => 'Mendukung upaya reklamasi lahan pascatambang yang bertanggung jawab.',
                'image' => 'carousel/01JKGNHQ0YJR9TQC72PDHZ7E97.jpg',
                'order' => 3,
            ],
            [
                'title' => 'Menjaga Ketahanan Energi',
                'subtitle' => 'Berperan dalam distribusi dan ketahanan energi untuk masyarakat.',
                'image' => 'carousel/01JMED5V7VRFW44ANG5H4V9K8Z.jpg',
                'order' => 4,
            ],
            [
                'title' => 'Bersama Membangun dari Lapangan',
                'subtitle' => 'Berdiri bersama para pekerja di garis depan sektor energi dan pertambangan.',
                'image' => 'carousel/01JKWG4DJ1VKDM2W3MNCGD8353.jpg',
                'order' => 5,
            ],
            [
                'title' => 'Sinergi untuk Masa Depan Energi',
                'subtitle' => 'Menghimpun kekuatan anggota untuk pengembangan usaha yang berkelanjutan.',
                'image' => 'carousel/01JMA86SVCFMW8702H1HNBT797.jpg',
                'order' => 6,
            ],
        ];

        foreach ($items as $item) {
            CarouselItem::updateOrCreate(['title' => $item['title']], $item);
        }
    }
}
