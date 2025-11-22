@extends('layouts.app')

@push('styles')
    <style>
        /* Hero Section */
        .about-hero {
            position: relative;
            height: 60vh;
            min-height: 400px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('images/rapat3.jpg') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: white;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            max-width: 600px;
        }

        /* Section Styling */
        .about-section {
            padding: 2rem 0 5rem 0;
        }

        .about-section:nth-child(even) {
            background-color: #f8f9fa;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
            color: #333;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
        }

        /* Timeline */
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            width: 2px;
            height: 100%;
            background: #FFD700;
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 3rem;
            width: calc(50% - 30px);
        }

        .timeline-item:nth-child(odd) {
            left: 0;
        }

        .timeline-item:nth-child(even) {
            left: 50%;
            margin-left: 30px;
        }

        .timeline-content {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .timeline-year {
            position: absolute;
            top: 0;
            background: #FFD700;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: black;
            font-weight: 600;
        }

        .timeline-item:nth-child(odd) .timeline-year {
            right: -80px;
        }

        .timeline-item:nth-child(even) .timeline-year {
            left: -80px;
        }

        /* Info Cards */
        .info-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-icon {
            font-size: 2.5rem;
            color: #FFD700;
            margin-bottom: 1rem;
        }

        /* Legal Section */
        .legal-document {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #FFD700;
        }

        .legal-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .legal-number {
            color: #666;
            font-size: 0.9rem;
        }

        .legal-link {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 15px;
            background-color: #FFD700;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .legal-link:hover {
            background-color: #e6c200;
            transform: translateY(-2px);
        }

        /* Resources Section */
        .resource-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .resource-icon {
            font-size: 3rem;
            color: #FFD700;
            margin-bottom: 1rem;
        }

        .resource-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .resource-title {
            font-size: 1.1rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .timeline::before {
                left: 0;
            }

            .timeline-item {
                width: calc(100% - 60px);
                left: 60px !important;
                margin-left: 0 !important;
            }

            .timeline-year {
                left: -90px !important;
            }

            .timeline-item:nth-child(odd) .timeline-year {
                right: unset;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Tentang KEP</h1>
                <p class="hero-subtitle">Menjadi koperasi terkemuka dalam bidang energi dan pertambangan di Indonesia.</p>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section class="about-section">
        <div class="container">
            <h2 class="section-title">Sejarah Kami</h2>
            <p class="section-subtitle">Perjalanan KEP dalam membangun industri energi dan pertambangan Indonesia sejak tahun
                1997.</p>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">1997</div>
                    <div class="timeline-content">
                        <h4>Pendirian KEP</h4>
                        <p>Koperasi Energi dan Pertambangan (KEP) awalnya didirikan dengan nama Koperasi Perhimpunan Purna
                            Karyawan Departemen Pertambangan dan Energi unit Departemen (KPPDPE) "Purna Bhakti" pada 10
                            April 1997 oleh para pensiunan Departemen Pertambangan dan Energi (DPE).
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">1998</div>
                    <div class="timeline-content">
                        <h4>Legalitas Koperasi</h4>
                        <p>KPPDPE "Purna Bhakti" memperoleh legalitas melalui Surat Keputusan Departemen Koperasi dan
                            Pembinaan Pengusaha Kecil No. 50/BH/KWK.9/I/1998, menandai pengakuan resmi koperasi ini sebagai
                            badan usaha.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2006</div>
                    <div class="timeline-content">
                        <h4>Anggaran Dasar Koperasi</h4>
                        <p>Terjadi perubahan anggaran dasar koperasi, yang disahkan melalui SK Menteri Negara Koperasi,
                            Usaha Kecil dan Menengah No. 083/BH/PAD/-1.82/VI/2006. Perubahan ini menyesuaikan struktur dan
                            tata kelola koperasi agar lebih profesional dan adaptif terhadap perkembangan sektor energi dan
                            pertambangan.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-content">
                        <h4>Anggaran Dasar Terbaru</h4>
                        <p>Pengesahan perubahan anggaran dasar terbaru dilakukan melalui SK Kemenkumham No
                            AHU-0001504.AH.01.27 tanggal 29 Desember 2020, memperkuat legalitas dan memperluas cakupan usaha
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
                        <p class="legal-number">50/BH/KWK.9/I/1998</p>
                        <p>Koperasi Perhimpunan Purna Karyawan Departemen Pertambangan dan Energi Unit Departemen (KPPDPE) "Purna Bhakti" tanggal 10 April 1997</p>
                        <a href="https://drive.google.com/file/d/1zpM2s_32_UU-3pdyaLsGYgAm9w0z-WqC/view" class="legal-link">
                            <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen
                        </a>
                    </div>

                    <div class="legal-document">
                        <h4 class="legal-title">Surat Keputusan</h4>
                        <p class="legal-number">No.50/BH/KWK.9/I/1998</p>
                        <p>SK Dep Koperasi dan Pembinaan Pengusaha Kecil</p>
                        <a href="https://drive.google.com/file/d/1ZJQUvuG-BIkf1xztbhO7Dt_zDPw9Sbyg/view?usp=sharing" class="legal-link">
                            <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen
                        </a>
                    </div>

                    <div class="legal-document">
                        <h4 class="legal-title">Sertifikasi ISO</h4>
                        <p class="legal-number">No. 083/BH/PAD/-1.82/VI/2006</p>
                        <p>SK Menteri Negara Koperasi, Usaha Kecil dan Menengah tentang perubahan anggaran dasar</p>
                        <a href="https://drive.google.com/file/d/1mjvzF8ay89AuCPLBMuXOuyaGaQ9amMAO/view?usp=sharing" class="legal-link">
                            <i class="fas fa-file-certificate me-1"></i> Lihat Sertifikat
                        </a>
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
                        <p>Jl. Ciledug Raya Kav. 109, Cipulir, Kebayoran Lama<br>
                            Jakarta Selatan 12230<br>
                            Indonesia</p>

                        <h5 class="mt-4">Kontak:</h5>
                        <p><i class="fas fa-phone me-2"></i>(021) 739-8422</p>
                        <p><i class="fas fa-envelope me-2"></i>info@kep.co.id</p>

                        <div class="mt-4">
                            <a href="#" class="btn btn-warning">Hubungi Kami</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <img src="{{ asset('images/rapat2.jpg') }}" alt="KEP Office" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>
@endsection
