<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Seed the single Company row the storefront depends on.
     *
     * welcome/about/checkout/the auth pages all read $company (see
     * ViewServiceProvider) and Company::first() being null crashes or
     * silently blanks large parts of those pages. firstOrCreate() keeps
     * this idempotent — running the seeder again (or on an install that
     * already has a Company row) won't duplicate or overwrite it.
     *
     * "Koperasi Energi dan Pertambangan (KEP)" is fictional demo content for this
     * portfolio build — name, history, legal numbers, address and logo are
     * all made up (see about.blade.php's timeline/legal sections too), so
     * the site has a complete, presentable look without a real company's
     * data attached. Swap it for the real thing via the admin panel
     * (Content > Companies) when using this project for an actual business.
     */
    public function run(): void
    {
        Company::firstOrCreate([], [
            'name' => 'Koperasi Energi dan Pertambangan (KEP)',
            'description' => 'Koperasi Energi dan Pertambangan (KEP) adalah koperasi multi-usaha yang menghimpun dan '
                . 'memberdayakan para profesional di sektor energi dan pertambangan melalui simpan pinjam, '
                . 'unit usaha ritel, dan layanan pendukung bagi anggotanya di seluruh Indonesia.',
            'vision' => 'Menjadi koperasi terkemuka yang menyejahterakan anggota melalui inovasi usaha di '
                . 'sektor energi dan pertambangan.',
            'mission' => "Menghimpun dan mengelola simpanan anggota secara profesional dan transparan.\n"
                . "Mengembangkan unit usaha yang relevan dengan kebutuhan anggota di sektor energi dan pertambangan.\n"
                . "Meningkatkan kompetensi anggota melalui pelatihan dan kemitraan strategis.",
            'history' => 'Sejak didirikan, KEP telah bertumbuh dari koperasi simpan pinjam sederhana menjadi '
                . 'koperasi multi-usaha dengan beragam layanan bagi anggotanya.',
            'email' => 'info@example.com',
            'phone' => '021-0000000',
            'address' => "Jl. Energi Raya No. 88\nJakarta Selatan 12440\nIndonesia",
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'KEP',
            'logo' => 'company/logo.svg',
            'image' => 'company/office.svg',
        ]);
    }
}
