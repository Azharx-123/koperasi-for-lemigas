@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/about.css')
@endpush

@section('title', 'Tentang Kami')
@section('meta_description', 'Profil, visi, misi, dan sejarah ' . $company->name . '.')

@section('content')
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Tentang KEP</h1>
                <p class="hero-subtitle">Menjadi koperasi terkemuka dalam bidang energi dan pertambangan di Indonesia.</p>
            </div>
        </div>
        <span class="hero-scroll-hint" aria-hidden="true"><i class="fas fa-chevron-down"></i></span>
    </section>

    @if (!empty($company->vision) || !empty($company->mission))
        <!-- Vision & Mission -->
        <section class="about-section">
            <div class="container">
                <h2 class="section-title">Visi &amp; Misi</h2>
                <p class="section-subtitle">Arah dan komitmen kami dalam melayani anggota di sektor energi dan
                    pertambangan.</p>

                <div class="row g-4">
                    @if (!empty($company->vision))
                        <div class="col-md-6">
                            <div class="info-card h-100">
                                <div class="info-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <h4>Visi</h4>
                                <p>{!! nl2br(e($company->vision)) !!}</p>
                            </div>
                        </div>
                    @endif

                    @if (!empty($company->mission))
                        <div class="col-md-6">
                            <div class="info-card h-100">
                                <div class="info-icon">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <h4>Misi</h4>
                                <p>{!! nl2br(e($company->mission)) !!}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- History Section -->
    <section class="about-section">
        <div class="container">
            <h2 class="section-title">Sejarah Kami</h2>
            <p class="section-subtitle">Perjalanan KEP dalam membangun industri energi dan pertambangan Indonesia sejak tahun
                2003.</p>

            @if (!empty($company->history))
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <p class="mb-5">{!! nl2br(e($company->history)) !!}</p>
                    </div>
                </div>
            @endif

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">2003</div>
                    <div class="timeline-content">
                        <h4>Pendirian KEP</h4>
                        <p>Koperasi Energi dan Pertambangan (KEP) didirikan pada 12 Agustus 2003 oleh sekelompok profesional di
                            sektor energi dan pertambangan yang ingin menghimpun simpanan anggota menjadi permodalan
                            usaha bersama.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2005</div>
                    <div class="timeline-content">
                        <h4>Legalitas Koperasi</h4>
                        <p>KEP memperoleh badan hukum melalui Surat Keputusan Dinas Koperasi dan UKM
                            No. 118/BH/KDK.10/VIII/2005, menandai pengakuan resmi koperasi ini sebagai badan usaha.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2012</div>
                    <div class="timeline-content">
                        <h4>Anggaran Dasar Koperasi</h4>
                        <p>Terjadi perubahan anggaran dasar koperasi, yang disahkan melalui SK Menteri Koperasi dan Usaha
                            Kecil Menengah No. 021/BH/PAD/X.5/III/2012. Perubahan ini menyesuaikan struktur dan
                            tata kelola koperasi agar lebih profesional dan adaptif terhadap perkembangan sektor energi dan
                            pertambangan.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2022</div>
                    <div class="timeline-content">
                        <h4>Anggaran Dasar Terbaru</h4>
                        <p>Pengesahan perubahan anggaran dasar terbaru dilakukan melalui SK Kemenkumham No.
                            AHU-0004732.AH.01.29 tanggal 14 Juli 2022, memperkuat legalitas dan memperluas cakupan usaha
                            koperasi menjadi Koperasi Jasa Energi dan Pertambangan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Objectives -->
    <section class="about-section bg-light">
        <div class="container">
            <h2 class="section-title">Tujuan Koperasi</h2>
            <p class="section-subtitle">Komitmen kami dalam mengembangkan ekonomi anggota di sektor energi dan pertambangan.
            </p>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h4>Pemberdayaan Anggota</h4>
                        <p>Mengembangkan potensi dan kesejahteraan anggota koperasi dalam bidang energi dan pertambangan.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Pengembangan SDM</h4>
                        <p>Meningkatkan kompetensi dan keahlian sumber daya manusia dalam industri energi dan pertambangan.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4>Kerjasama Strategis</h4>
                        <p>Membangun kemitraan strategis dengan pelaku industri energi dan pertambangan nasional dan
                            internasional.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Legal Status -->
    <section class="about-section">
        <div class="container">
            <h2 class="section-title">Legalitas Koperasi</h2>
            <p class="section-subtitle">Dasar hukum dan legalitas operasional KEP.</p>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="legal-document">
                        <h4 class="legal-title">Akta Pendirian</h4>
                        <p class="legal-number">118/BH/KDK.10/VIII/2005</p>
                        <p>Koperasi Energi dan Pertambangan (KEP), didirikan 12 Agustus 2003</p>
                        <span class="legal-link">
                            <i class="fas fa-file-pdf me-1"></i> Dokumen tersedia di kantor kami
                        </span>
                    </div>

                    <div class="legal-document">
                        <h4 class="legal-title">Surat Keputusan</h4>
                        <p class="legal-number">No. 021/BH/PAD/X.5/III/2012</p>
                        <p>SK Menteri Koperasi dan Usaha Kecil Menengah</p>
                        <span class="legal-link">
                            <i class="fas fa-file-pdf me-1"></i> Dokumen tersedia di kantor kami
                        </span>
                    </div>

                    <div class="legal-document">
                        <h4 class="legal-title">Sertifikasi ISO</h4>
                        <p class="legal-number">No. AHU-0004732.AH.01.29</p>
                        <p>SK Kemenkumham tentang perubahan anggaran dasar</p>
                        <span class="legal-link">
                            <i class="fas fa-certificate me-1"></i> Dokumen tersedia di kantor kami
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Resources -->
    <section class="about-section bg-light">
        <div class="container">
            <h2 class="section-title">Sumber Daya Koperasi</h2>
            <p class="section-subtitle">Kekuatan dan kapabilitas KEP dalam industri energi dan pertambangan.</p>

            <div class="row">
                <div class="col-md-3">
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="resource-number">500+</div>
                        <div class="resource-title">Anggota Aktif</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="resource-number">15</div>
                        <div class="resource-title">Unit Usaha</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="resource-number">50+</div>
                        <div class="resource-title">Mitra Koperasi</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="resource-card">
                        <div class="resource-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="resource-number">5</div>
                        <div class="resource-title">Kantor Cabang</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Location -->
    <section class="about-section" id="company-location">
        <div class="container">
            <h2 class="section-title">Lokasi Kami</h2>
            <p class="section-subtitle">Kantor pusat dan fasilitas KEP.</p>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="info-card">
                        <h4><i class="fas fa-map-marker-alt text-warning me-2"></i>Kantor Pusat</h4>
                        <p>{!! nl2br(e($company->address)) !!}</p>

                        <h5 class="mt-4">Kontak:</h5>
                        <p><i class="fas fa-phone me-2"></i>{{ $company->phone }}</p>
                        <p><i class="fas fa-envelope me-2"></i>{{ $company->email }}</p>

                        <div class="mt-4">
                            <a href="mailto:{{ $company->email }}" class="btn btn-warning">Hubungi Kami</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <img src="{{ \App\Helpers\ImageHelper::url($company->image) }}" alt="Kantor {{ $company->name }}" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>
@endsection
