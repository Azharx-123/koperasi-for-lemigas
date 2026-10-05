@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/welcome.css')
@endpush

@section('content')
    <div id="loading-screen" class="loading-screen">
        <div class="logo-container">
            <img src="{{ \App\Helpers\ImageHelper::url($company->logo) }}" alt="{{ $company->name }} Logo" class="company-logo">
        </div>
        <div class="loading-bar">
            <div class="loading-progress"></div>
        </div>
    </div>

    <button id="scrollToTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas halaman">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Dark Mode Toggle -->
    <button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle Dark Mode">
        <i class="fas fa-moon"></i>
        <i class="fas fa-sun"></i>
    </button>

    <!-- Carousel -->
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="false"
        data-aos="fade-in">
        <div class="carousel-indicators">
            @foreach ($carousel_items as $index => $item)
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}"
                    class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach ($carousel_items as $item)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <img src="{{ \App\Helpers\ImageHelper::url($item->image) }}" class="d-block w-100" alt="{{ $item->title }}"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                    <div class="carousel-caption">
                        <h2>{{ $item->title }}</h2>
                        <p>{{ $item->subtitle }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev"
            aria-label="Slide sebelumnya">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next"
            aria-label="Slide berikutnya">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <!-- Separator -->
    <div class="light-separator" data-aos="fade-in" aria-hidden="true">
        <div class="light-beam light-beam-left"></div>
        <div class="light-beam light-beam-right"></div>
    </div>

    <!-- KEP Description Section -->
    <section class="kep-section" data-aos="fade-up" id="about-section">
        <div class="container">
            <h2 class="kep-title" data-aos="fade-down">Tentang Koperasi Energi dan Pertambangan</h2>
            <div class="kep-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="kep-card" data-aos="fade-up" data-aos-delay="300">
                <div class="kep-image" data-aos="zoom-in" data-aos-delay="400">
                    <img src="{{ \App\Helpers\ImageHelper::url($company->image) }}" alt="KEP Headquarters" loading="lazy">
                </div>
                <div class="kep-content">
                    <p class="kep-description" data-aos="fade-up" data-aos-delay="500">
                        Koperasi Energi dan Pertambangan adalah lembaga yang berfokus pada pengembangan dan pemberdayaan
                        sektor energi dan pertambangan.
                        Kami berkomitmen untuk meningkatkan daya saing dan ketahanan energi melalui sinergi antara koperasi
                        dan masyarakat.
                    </p>
                    <div class="kep-features">
                        @foreach ($features->where('is_active', true)->sortBy('order') as $feature)
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class="{{ $feature->icon }} fa-2x text-dark"></i>
                                </div>
                                <h4 class="feature-title">{{ $feature->title }}</h4>
                                <p class="feature-text">{{ $feature->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Welcome Section -->
    <section class="section-elegant" data-aos="fade-up" id="features-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Mengapa Memilih Kami?</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="row g-4 justify-content-center">
                @foreach ($features as $feature)
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="feature-box">
                            <div class="feature-icon">
                                <i class="{{ $feature->icon }} fa-2x text-dark"></i>
                            </div>
                            <h4 class="feature-title">{{ $feature->title }}</h4>
                            <p class="feature-text">{{ $feature->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- View More Section -->
    <section class="hero-banner" data-aos="fade-up" id="hero-section"
        style="background-image: url('{{ \App\Helpers\ImageHelper::url($company->image) }}')">
        <div class="hero-content">
            <div class="container-fluid p-0">
                <div class="row m-0">
                    <div class="col-lg-6 col-md-12 p-0" data-aos="fade-right" data-aos-delay="200">
                        <div class="hero-text-container">
                            <h1 class="hero-title">Tentang Kami</h1>
                            <p class="hero-description">
                                Kami berfokus pada pengelolaan sumber daya energi dan pertambangan untuk mendukung
                                kesejahteraan masyarakat.
                            </p>
                            <a href="{{ route('about') }}" class="btn-view-more">LIHAT SELENGKAPNYA</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 p-0" data-aos="fade-left" data-aos-delay="300">
                        <div class="hero-action-container">
                            <a href="{{ route('products.index') }}" class="product-link">
                                atau Mulai Cari Produk
                                <div class="animated-arrows">
                                    <span class="arrow arrow-first" aria-hidden="true"></span>
                                    <span class="arrow arrow-second" aria-hidden="true"></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Best Products Section -->
    <section class="section-elegant" data-aos="fade-up" id="products-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Produk Unggulan Kami</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="row g-4 justify-content-center">
                @foreach ($best_products as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up"
                        data-aos-delay="{{ $loop->index * 200 }}">
                        <div class="product-card">
                            @if ($product->image)
                                <img src="{{ \App\Helpers\ImageHelper::url($product->image) }}" class="product-image"
                                    alt="{{ $product->name }}" loading="lazy">
                            @endif
                            <div class="product-content">
                                <h5 class="mb-3">{{ $product->name }}</h5>
                                <p class="product-description">{{ $product->short_description }}</p>
                                <div class="product-footer">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="product-price">{{ $product->formatted_price }}</span>
                                        <span class="product-badge">{{ $product->sold_count }}+ Terjual</span>
                                    </div>
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-detail-btn">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View More Button Container -->
            <div class="view-more-container" data-aos="fade-up" data-aos-delay="300">
                <a href="{{ route('products.index') }}" class="view-more-btn">
                    <span class="view-more-text">Lihat Semua Produk</span>
                    <span class="view-more-icon">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>


    <!-- Persona Section -->
    <section class="persona-section" data-aos="fade-up" id="persona-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Siapa yang Cocok dengan Produk Kami?</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="100"></div>
            <div class="row g-4">
                @foreach ($personas as $persona)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="persona-card h-100">
                            <div class="persona-icon-container">
                                <div class="persona-icon-bg"></div>
                                <div class="persona-icon">
                                    <i class="{{ $persona->icon }} fa-lg"></i>
                                </div>
                            </div>
                            <span class="persona-counter" data-count="{{ $persona->count }}">0+</span>
                            <h4 class="persona-title">{{ $persona->title }}</h4>
                            <p class="persona-subtitle">{{ $persona->subtitle }}</p>
                            <p class="persona-description">{{ $persona->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Sponsor Section -->
    <section class="section-elegant" data-aos="fade-up" id="sponsors-section">
        <div class="container">
            <h2 class="section-title-elegant" data-aos="fade-down">Partner & Sponsor Kami</h2>
            <div class="elegant-separator" data-aos="zoom-in" data-aos-delay="200"></div>
            <div class="sponsor-wrapper" data-aos="fade-up" data-aos-delay="300">
                <div class="sponsor-scroll-container">
                    <div class="sponsor-track" id="sponsorTrack">
                        <!-- Original sponsors -->
                        @foreach ($sponsors as $sponsor)
                            <div class="sponsor-item">
                                <a href="{{ $sponsor->website_url }}" target="_blank" rel="noopener noreferrer"
                                    aria-label="{{ $sponsor->name }}">
                                    <img src="{{ \App\Helpers\ImageHelper::url($sponsor->logo) }}" alt="{{ $sponsor->name }}"
                                        class="sponsor-logo" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                        <!-- Duplicate sponsors for seamless loop -->
                        @foreach ($sponsors as $sponsor)
                            <div class="sponsor-item">
                                <a href="{{ $sponsor->website_url }}" target="_blank" rel="noopener noreferrer"
                                    aria-label="{{ $sponsor->name }}">
                                    <img src="{{ \App\Helpers\ImageHelper::url($sponsor->logo) }}" alt="{{ $sponsor->name }}"
                                        class="sponsor-logo" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Closing Section -->
    <section class="closing-section" data-aos="fade-up" id="contact-section">
        <div class="closing-background" style="background-image: url('{{ asset('images/construction-2.webp') }}')">
        </div>
        <div class="closing-overlay"></div>
        <div class="container closing-content">
            <div class="closing-grid">
                <div class="closing-text" data-aos="fade-right" data-aos-delay="200">
                    <h2 class="closing-heading">Mari Bergabung dengan Kami</h2>
                    <p class="closing-description">
                        Bergabunglah dalam revolusi energi dan pertambangan yang lebih berkelanjutan bersama kami.
                    </p>
                    <a href="{{ route('about') }}#company-location" class="closing-cta">
                        Hubungi Kami
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="closing-image-container" data-aos="fade-left" data-aos-delay="300">
                    <img src="{{ asset('images/engineer.webp') }}" alt="Professional Engineer"
                        class="closing-person-image" loading="lazy">
                </div>
            </div>
        </div>
        <div class="closing-accent" aria-hidden="true"></div>
        <div class="closing-accent" aria-hidden="true"></div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfigurasi loading screen yang lebih efisien
            const loadingScreen = document.getElementById('loading-screen');
            const startTime = new Date().getTime();
            const minLoadTime = 800; // Waktu minimum loading dalam milliseconds

            // Fungsi untuk menghilangkan loading screen
            const hideLoadingScreen = () => {
                const currentTime = new Date().getTime();
                const elapsedTime = currentTime - startTime;

                // Pastikan waktu minimum terpenuhi
                if (elapsedTime >= minLoadTime) {
                    loadingScreen.classList.add('fade-out');
                    setTimeout(() => {
                        loadingScreen.remove();
                    }, 300);
                } else {
                    // Tunggu hingga waktu minimum tercapai
                    setTimeout(hideLoadingScreen, minLoadTime - elapsedTime);
                }
            };

            // Aktifkan setelah semua konten dimuat
            window.addEventListener('load', hideLoadingScreen);

            // Fallback jika terjadi masalah loading
            setTimeout(hideLoadingScreen, 3000);

            // Tombol scroll to top yang diperbarui
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');

            // Tampilkan/sembunyikan tombol berdasarkan posisi scroll
            const toggleScrollBtn = () => {
                if (window.pageYOffset > 400) {
                    scrollToTopBtn.classList.add('visible');
                } else {
                    scrollToTopBtn.classList.remove('visible');
                }
            };

            window.addEventListener('scroll', toggleScrollBtn);

            // Smooth scroll ke atas ketika tombol diklik
            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            const darkModeToggle = document.getElementById('darkModeToggle');
            const htmlElement = document.documentElement;

            // Check for saved preference or system preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' ||
                (savedTheme === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                htmlElement.classList.add('dark-mode');
            }

            // Update button appearance based on current mode
            updateButtonAppearance();

            // Toggle dark mode on button click
            darkModeToggle.addEventListener('click', function() {
                htmlElement.classList.toggle('dark-mode');

                // Save preference to localStorage
                if (htmlElement.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }

                // Update button appearance
                updateButtonAppearance();
            });

            // Function to update button appearance
            function updateButtonAppearance() {
                if (htmlElement.classList.contains('dark-mode')) {
                    darkModeToggle.setAttribute('aria-label', 'Switch to Light Mode');
                    darkModeToggle.style.backgroundColor = 'rgb(var(--yellow-primary))';
                    darkModeToggle.style.color = 'rgb(var(--black))';
                } else {
                    darkModeToggle.setAttribute('aria-label', 'Switch to Dark Mode');
                    darkModeToggle.style.backgroundColor = 'rgb(var(--gray-800))';
                    darkModeToggle.style.color = 'rgb(var(--yellow-primary))';
                }
            }

            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                // Only change if user hasn't manually set a preference
                if (!localStorage.getItem('theme')) {
                    if (e.matches) {
                        htmlElement.classList.add('dark-mode');
                    } else {
                        htmlElement.classList.remove('dark-mode');
                    }
                    updateButtonAppearance();
                }
            });

            // Perbaikan efek parallax - metode yang lebih reliable
            const heroSection = document.querySelector('.hero-banner');

            if (heroSection) {
                // Pastikan gambar latar belakang sudah dimuat sebelum menerapkan efek
                const bgUrl = window.getComputedStyle(heroSection).backgroundImage;

                if (bgUrl && bgUrl !== 'none') {
                    // Simpan posisi background awal
                    const initialBgPosition = window.getComputedStyle(heroSection).backgroundPosition;

                    // Fungsi parallax yang diperbarui
                    function parallaxEffect() {
                        // Dapatkan posisi scroll dan posisi elemen
                        const scrollPosition = window.pageYOffset;
                        const heroRect = heroSection.getBoundingClientRect();

                        // Periksa apakah hero section dalam viewport
                        const isInView = (
                            heroRect.bottom > 0 &&
                            heroRect.top < window.innerHeight
                        );

                        if (isInView) {
                            // Hitung efek parallax berdasarkan seberapa jauh kita scroll
                            // Tingkatkan multiplier untuk efek yang lebih terlihat (dari 0.3 ke 0.5)
                            const parallaxOffset = scrollPosition * 0.5;

                            // Terapkan efek parallax ke background position
                            heroSection.style.backgroundPosition = `center calc(50% + ${parallaxOffset}px)`;
                        }
                    }

                    // Jalankan sekali saat halaman dimuat
                    parallaxEffect();

                    // Tambahkan event listener dengan throttling untuk performa
                    let isScrolling = false;
                    window.addEventListener('scroll', function() {
                        if (!isScrolling) {
                            window.requestAnimationFrame(function() {
                                parallaxEffect();
                                isScrolling = false;
                            });
                            isScrolling = true;
                        }
                    });

                    // Tambahkan handler untuk resize window
                    window.addEventListener('resize', parallaxEffect);
                } else {
                    console.warn('Hero banner tidak memiliki background image');
                }
            } else {
                console.warn('Hero banner element tidak ditemukan');
            }

            // Inisialisasi animasi sponsor
            const initSponsors = () => {
                const track = document.getElementById('sponsorTrack');
                if (!track) return;

                const items = track.querySelectorAll('.sponsor-item');
                if (items.length === 0) return;

                // Hitung lebar total dan sesuaikan durasi animasi
                let totalWidth = 0;
                const firstHalfItems = Array.from(items).slice(0, items.length / 2);

                firstHalfItems.forEach(item => {
                    totalWidth += item.offsetWidth;
                });

                const duration = Math.max(20, totalWidth / 50);
                track.style.setProperty('--duration', `${duration}s`);
            };

            // Inisialisasi animasi counter
            const initCounters = () => {
                const counterElements = document.querySelectorAll('.persona-counter');

                // Fungsi untuk menganimasikan counter saat dalam viewport
                const animateCounters = () => {
                    counterElements.forEach(counter => {
                        if (isInViewport(counter)) {
                            const target = parseInt(counter.getAttribute('data-count'));
                            const current = parseInt(counter.textContent);

                            if (current < target) {
                                // Gunakan proporsi untuk penambahan yang lebih smooth
                                const remaining = target - current;
                                const increment = Math.max(1, Math.ceil(remaining / 15));
                                const newValue = Math.min(current + increment, target);

                                counter.textContent = newValue + '+';

                                if (newValue < target) {
                                    // Terapkan penundaan berdasarkan seberapa dekat ke target
                                    const delay = 1000 / (target / 10);
                                    setTimeout(() => requestAnimationFrame(animateCounters), delay);
                                }
                            }
                        }
                    });
                };

                // Observer API untuk mendeteksi elemen dalam viewport
                const observerOptions = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 0.1
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounters();
                        }
                    });
                }, observerOptions);

                counterElements.forEach(counter => {
                    observer.observe(counter);
                });
            };

            // Fungsi utility untuk mendeteksi elemen dalam viewport
            function isInViewport(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.bottom >= 0 &&
                    rect.left <= (window.innerWidth || document.documentElement.clientWidth) &&
                    rect.right >= 0
                );
            }

            // Inisialisasi efek magnetic untuk kartu
            const initMagneticEffect = () => {
                // Hanya terapkan pada perangkat non-touch
                if (window.matchMedia('(hover: hover)').matches) {
                    const personaCards = document.querySelectorAll('.persona-card');

                    personaCards.forEach(card => {
                        // Mouse move pada kartu
                        card.addEventListener('mousemove', function(e) {
                            const cardRect = card.getBoundingClientRect();
                            const cardCenterX = cardRect.left + cardRect.width / 2;
                            const cardCenterY = cardRect.top + cardRect.height / 2;

                            const mouseX = e.clientX;
                            const mouseY = e.clientY;

                            // Hitung jarak dari pusat (sebagai persentase)
                            const distanceX = (mouseX - cardCenterX) / (cardRect.width / 2);
                            const distanceY = (mouseY - cardCenterY) / (cardRect.height / 2);

                            // Batasi efek kemiringan
                            const tiltLimitX = 5; // derajat
                            const tiltLimitY = 5; // derajat

                            const tiltX = -1 * distanceY * tiltLimitY;
                            const tiltY = distanceX * tiltLimitX;

                            // Terapkan efek kemiringan dengan transisi yang lebih halus
                            card.style.transition = 'transform 0.1s ease-out';
                            card.style.transform =
                                `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateY(-10px) scale(1.02)`;

                            // Gerakkan ikon mengikuti mouse
                            const icon = card.querySelector('.persona-icon');
                            if (icon) {
                                icon.style.transition = 'transform 0.1s ease-out';
                                icon.style.transform =
                                    `translate(${distanceX * 5}px, ${distanceY * 5}px) rotate(10deg) scale(1.1)`;
                            }
                        });

                        // Reset posisi card saat mouse leave
                        card.addEventListener('mouseleave', function() {
                            // Reset posisi kartu dengan halus
                            card.style.transition = 'transform 0.5s ease-out';
                            card.style.transform = '';

                            // Reset posisi ikon
                            const icon = card.querySelector('.persona-icon');
                            if (icon) {
                                icon.style.transition = 'transform 0.5s ease-out';
                                icon.style.transform = '';
                            }
                        });
                    });
                }
            };

            // Lazy load gambar untuk meningkatkan performa
            const lazyLoadImages = () => {
                if ('loading' in HTMLImageElement.prototype) {
                    // Gunakan native lazy loading jika browser mendukung
                    const images = document.querySelectorAll('img[loading="lazy"]');
                    images.forEach(img => {
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                    });
                } else {
                    // Fallback untuk browser yang tidak mendukung
                    const lazyImages = document.querySelectorAll('img[data-src]');
                    const imageObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                imageObserver.unobserve(img);
                            }
                        });
                    });

                    lazyImages.forEach(img => {
                        imageObserver.observe(img);
                    });
                }
            };

            // Inisialisasi semua fungsi
            setTimeout(() => {
                initSponsors();
                initCounters();
                initMagneticEffect();
                lazyLoadImages();
            }, 100);

            // Animasi gambar carousel
            const carouselItems = document.querySelectorAll('.carousel-item');
            carouselItems.forEach(item => {
                item.addEventListener('transitionend', function(e) {
                    if (e.target === this && this.classList.contains('active')) {
                        const img = this.querySelector('img');
                        if (img) {
                            // Hanya animate gambar saat slide menjadi aktif
                            img.style.transition = 'transform 8s ease';
                            img.style.transform = 'scale(1.08)';
                        }
                    }
                });
            });

            // Perbaikan scroll smooth untuk link anchor
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href');
                    if (targetId !== '#' && document.querySelector(targetId)) {
                        e.preventDefault();
                        document.querySelector(targetId).scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
@endpush
