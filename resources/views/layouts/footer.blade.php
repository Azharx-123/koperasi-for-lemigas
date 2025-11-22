<style>
 :root {
            --yellow-primary: 255, 215, 0;
            --yellow-light: 255, 229, 92;
            --yellow-dark: 178, 151, 0;
            --gray-100: 248, 249, 250;
            --gray-200: 233, 236, 239;
            --gray-300: 222, 226, 230;
            --gray-600: 108, 117, 125;
            --gray-800: 52, 58, 64;
            --black: 33, 37, 41;
        }

.footer-section {
    position: relative;
    background: linear-gradient(135deg, rgb(var(--gray-800)) 0%, rgb(var(--black)) 100%);
    font-family: 'Inter', sans-serif;
}

.footer-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 1.2rem;
    color: rgb(var(--gray-100));
    position: relative;
    padding-bottom: 0.5rem;
}

.footer-title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background: linear-gradient(90deg, rgb(var(--yellow-primary)), rgb(var(--yellow-light)));
}

.footer-description {
    color: rgb(var(--gray-300));
    line-height: 1.6;
    font-size: 0.95rem;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(var(--gray-100), 0.1);
    color: rgb(var(--gray-100));
    border-radius: 50%;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: rgb(var(--yellow-primary));
    color: rgb(var(--black));
    transform: translateY(-3px);
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.8rem;
}

.footer-links a {
    color: rgb(var(--gray-300));
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.footer-links a:hover {
    color: rgb(var(--yellow-light));
    padding-left: 5px;
}

.footer-contact {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-contact li {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
    color: rgb(var(--gray-300));
    font-size: 0.95rem;
}

.footer-contact i {
    color: rgb(var(--yellow-primary));
    margin-top: 5px;
}

.newsletter-form .form-control {
    background: rgba(var(--gray-800), 0.5);
    border: 1px solid rgba(var(--gray-600), 0.3);
    color: rgb(var(--gray-100));
    padding: 0.6rem 1rem;
}

.newsletter-form .form-control::placeholder {
    color: rgb(var(--gray-300));
}

.newsletter-form .btn {
    padding: 0.6rem 1.2rem;
    background: linear-gradient(90deg, rgb(var(--yellow-primary)), rgb(var(--yellow-light)));
    border: none;
    color: rgb(var(--black));
}

.newsletter-form .btn:hover {
    background: linear-gradient(90deg, rgb(var(--yellow-light)), rgb(var(--yellow-primary)));
}

.footer-divider {
    margin: 2rem 0;
    border-color: rgba(var(--gray-600), 0.3);
}

.copyright {
    color: rgb(var(--gray-300));
    font-size: 0.9rem;
}

.footer-bottom-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: flex-end;
    gap: 2rem;
}

.footer-bottom-links a {
    color: rgb(var(--gray-300));
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
    color: rgb(var(--yellow-light));
}

@media (max-width: 768px) {
    .footer-bottom-links {
        justify-content: flex-start;
        margin-top: 1rem;
    }
    
    .copyright {
        text-align: center;
    }
    
    .footer-bottom-links {
        justify-content: center;
    }
}
</style>

<footer class="footer-section bg-dark text-light py-5">
    <div class="container">
        <!-- Main Footer Content -->
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-4">
                <div class="footer-widget">
                    <h5 class="footer-title mb-4">{{ $company->name }}</h5>
                    <p class="footer-description mb-4">
                        Pusat Penelitian dan Pengembangan Teknologi Minyak dan Gas Bumi yang berfokus pada inovasi dan pengembangan teknologi untuk masa depan industri migas.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <div class="footer-widget">
                    <h5 class="footer-title mb-4">Tautan Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="#produk">Produk</a></li>
                        <li><a href="#kategori">Kategori</a></li>
                        <li><a href="#persona">Layanan</a></li>
                    </ul>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h5 class="footer-title mb-4">Kontak Kami</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $company->address }}</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>{{ $company->phone }}</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>{{ $company->email }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h5 class="footer-title mb-4">Newsletter</h5>
                    <p class="mb-4">Berlangganan newsletter kami untuk mendapatkan informasi terbaru</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Anda" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <hr class="footer-divider">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright mb-0">
                        &copy; {{ date('Y') }} {{ $company->name }}. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6">
                    <ul class="footer-bottom-links">
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>