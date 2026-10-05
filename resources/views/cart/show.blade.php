@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/cart-show.css')
@endpush

@section('title', 'Keranjang Belanja')

@section('content')
    <!-- Scroll to top button -->
    <button id="scrollToTopBtn" class="scroll-top-btn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <section class="cart-section">
        <div class="container">
            <h2 class="cart-title" data-aos="fade-down">Keranjang Belanja</h2>
            <div class="cart-separator" data-aos="zoom-in" data-aos-delay="200"></div>

            @if (session('success'))
                <div class="alert alert-elegant alert-success" data-aos="fade-up" data-aos-delay="300">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-lg"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @if (count($cartItems) > 0)
                <div class="cart-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="table-responsive">
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%">Produk</th>
                                    <th style="width: 15%">Harga</th>
                                    <th style="width: 20%">Jumlah</th>
                                    <th style="width: 15%">Total</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $id => $item)
                                    <tr data-aos="fade-up" data-aos-delay="{{ 400 + $loop->index * 100 }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ \App\Helpers\ImageHelper::url($item['image']) }}" alt="{{ $item['name'] }}"
                                                    class="cart-item-image me-3">
                                                <div class="cart-item-name">{{ $item['name'] }}</div>
                                            </div>
                                        </td>
                                        <td class="cart-price" data-label="Harga">{{ \App\Helpers\CurrencyHelper::formatRupiah($item['price']) }}</td>
                                        <td data-label="Jumlah">
                                            <div class="d-flex align-items-center">
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                                    min="1" max="{{ $item['stock'] }}"
                                                    class="form-control cart-quantity-input"
                                                    data-original="{{ $item['quantity'] }}"
                                                    data-update-url="{{ route('cart.update', $item['pivot_id']) }}">
                                            </div>
                                        </td>
                                        <td class="cart-item-total" data-label="Total">
                                            {{ \App\Helpers\CurrencyHelper::formatRupiah($item['price'] * $item['quantity']) }}</td>
                                        <td data-label="Aksi">
                                            <form action="{{ route('cart.remove', $item['pivot_id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="cart-remove-btn"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="cart-footer">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between cart-total-row py-2">
                                    <span>Subtotal:</span>
                                    <span class="cart-total-amount">{{ \App\Helpers\CurrencyHelper::formatRupiah($total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cart-actions">
                    {{-- Tombol Lanjut Belanja (Tetap di Kiri) --}}
                    <a href="{{ route('products.index') }}" class="continue-shopping-btn">
                        <div class="icon-container">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                        <span class="btn-text">Lanjut Belanja</span>
                    </a>

                    {{-- Grup Tombol di Kanan --}}
                    <div class="d-flex align-items-center" style="gap: 1rem;">


                        {{-- Tombol Checkout: dual-function. Kalau ada perubahan qty yang
                             belum disimpan, klik ini submit dulu perubahannya (AJAX) baru
                             lanjut ke checkout; kalau tidak ada perubahan, ini cuma link
                             biasa (href asli tetap ada sebagai fallback kalau JS gagal). --}}
                        <a href="{{ route('checkout.index') }}" class="checkout-btn" id="checkout-btn">
                            <div class="icon-container">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <span class="btn-text" id="checkout-btn-text">Checkout</span>
                        </a>
                    </div>
                </div>

                <div id="cart-update-error" class="cart-update-error" style="display: none;"></div>
            @else
                    <div class="empty-cart-container" data-aos="fade-up" data-aos-delay="400">
                        <div class="empty-cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="empty-cart-title">Keranjang Belanja Kosong</h3>
                        <p class="empty-cart-text">Anda belum menambahkan produk ke keranjang</p>
                        <a href="{{ route('products.index') }}" class="start-shopping-btn">
                            <span class="start-shopping-text">Mulai Belanja</span>
                        </a>
                        </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Scroll to top button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');

            // Show/hide button based on scroll position
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.add('visible');
                } else {
                    scrollToTopBtn.classList.remove('visible');
                }
            });

            // Scroll to top when button is clicked
            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <script>
        // Tombol update per-baris sudah dihapus (lihat cart-quantity-input di
        // atas) — checkout-btn sekarang dual-function: kalau ada perubahan qty
        // yang belum disimpan, klik ini submit dulu semua perubahan (AJAX),
        // baru redirect ke checkout kalau semuanya berhasil. href asli tetap
        // dipertahankan sebagai fallback kalau JS gagal load (klik tetap
        // mengarah ke checkout, cuma skip simpan perubahan).
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = Array.from(document.querySelectorAll('.cart-quantity-input'));
            const checkoutBtn = document.getElementById('checkout-btn');
            const checkoutBtnText = document.getElementById('checkout-btn-text');
            const errorBox = document.getElementById('cart-update-error');

            if (!checkoutBtn || quantityInputs.length === 0) {
                return;
            }

            const checkoutUrl = checkoutBtn.getAttribute('href');

            function hasPendingChanges() {
                return quantityInputs.some(input => input.value !== input.dataset.original);
            }

            function refreshCheckoutBtnState() {
                if (hasPendingChanges()) {
                    checkoutBtnText.textContent = 'Submit Perubahan';
                    checkoutBtn.classList.add('has-pending-changes');
                } else {
                    checkoutBtnText.textContent = 'Checkout';
                    checkoutBtn.classList.remove('has-pending-changes');
                }
            }

            function showError(message) {
                errorBox.textContent = message;
                errorBox.style.display = 'block';
            }

            function hideError() {
                errorBox.style.display = 'none';
            }

            quantityInputs.forEach(input => {
                input.addEventListener('input', function() {
                    hideError();
                    refreshCheckoutBtnState();
                });
            });

            checkoutBtn.addEventListener('click', function(e) {
                const changedInputs = quantityInputs.filter(input => input.value !== input.dataset.original);

                // Tidak ada perubahan — biarkan link jalan seperti biasa (full
                // page navigation, tidak perlu preventDefault/fetch sama sekali).
                if (changedInputs.length === 0) {
                    return;
                }

                e.preventDefault();
                hideError();

                const originalText = checkoutBtnText.textContent;
                checkoutBtnText.textContent = 'Menyimpan...';
                checkoutBtn.classList.add('disabled');

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const requests = changedInputs.map(input =>
                    fetch(input.dataset.updateUrl, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ quantity: input.value }),
                        })
                        .then(response => response.json().then(data => ({ ok: response.ok, data })))
                );

                Promise.all(requests)
                    .then(results => {
                        const failed = results.find(result => !result.ok || !result.data.success);

                        if (failed) {
                            checkoutBtn.classList.remove('disabled');
                            checkoutBtnText.textContent = originalText;
                            showError(failed.data.message || 'Gagal menyimpan perubahan keranjang. Silakan coba lagi.');
                            return;
                        }

                        // Semua perubahan berhasil disimpan — baru lanjut ke checkout.
                        window.location.href = checkoutUrl;
                    })
                    .catch(() => {
                        checkoutBtn.classList.remove('disabled');
                        checkoutBtnText.textContent = originalText;
                        showError('Gagal menyimpan perubahan keranjang. Silakan coba lagi.');
                    });
            });
        });
    </script>
@endpush
