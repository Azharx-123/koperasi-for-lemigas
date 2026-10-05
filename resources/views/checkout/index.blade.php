@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/checkout-index.css')
@endpush

@section('title', 'Checkout')

@section('content')
    <div class="container py-5">
        <h2 class="section-title-elegant">Checkout</h2>
        <div class="elegant-separator"></div>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- #10: banner kecil buat tamu (checkout tanpa akun tetap didukung
             penuh) — ini cuma ajakan opsional, bukan penghalang. --}}
        @guest
            <div class="alert alert-info checkout-guest-banner">
                <span>
                    <i class="fas fa-info-circle me-2"></i>Sudah punya akun? Masuk supaya alamat pengiriman bisa
                    terisi otomatis dan pesanan tercatat di riwayat akun Anda.
                </span>
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Masuk</a>
            </div>
        @endguest

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Left Column - Customer Information -->
                <div class="col-lg-8">
                    <!-- Customer Information Card -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h3 class="checkout-card-title">
                                <i class="fas fa-user-circle me-2"></i>Informasi Pelanggan
                            </h3>
                        </div>
                        <div class="checkout-card-body">
                            {{-- #11: kalau user login & punya data profil tersimpan, tawarkan
                                 checkbox buat isi otomatis dari situ. Kalau login tapi belum
                                 pernah isi profil, kasih hint kecil ke halaman profil (bukan
                                 dipaksa). Tamu tidak lihat salah satu dari ini — banner ajakan
                                 login di atas sudah cukup. --}}
                            @auth
                                @if ($profile)
                                    <div class="form-check use-profile-toggle mb-4">
                                        <input class="form-check-input" type="checkbox" id="use-profile-data"
                                            checked>
                                        <label class="form-check-label" for="use-profile-data">
                                            Gunakan data profil saya
                                        </label>
                                    </div>
                                @else
                                    <p class="text-muted small mb-4 profile-hint">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Simpan alamat pengiriman di <a href="{{ route('profile.edit') }}">profil
                                            Anda</a> supaya form ini terisi otomatis lain kali.
                                    </p>
                                @endif
                            @endauth

                            <div class="row">
                                <div class="col-md-6 form-section">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', $profile['phone'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="company" class="form-label">Perusahaan (Opsional)</label>
                                    <input type="text" class="form-control" id="company" name="company"
                                        value="{{ old('company', $profile['company'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information Card -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h3 class="checkout-card-title">
                                <i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman
                            </h3>
                        </div>
                        <div class="checkout-card-body">
                            <div class="form-section">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $profile['address'] ?? '') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-section">
                                    <label for="province" class="form-label">Provinsi</label>
                                    @php $selectedProvince = old('province', $profile['province'] ?? ''); @endphp
                                    <select class="form-control" id="province" name="province" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach ([
                                            'Aceh', 'Bali', 'Banten', 'Bengkulu', 'DI Yogyakarta', 'DKI Jakarta',
                                            'Gorontalo', 'Jambi', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur',
                                            'Kalimantan Barat', 'Kalimantan Selatan', 'Kalimantan Tengah',
                                            'Kalimantan Timur', 'Kalimantan Utara', 'Kepulauan Bangka Belitung',
                                            'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara',
                                            'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Papua',
                                            'Papua Barat', 'Papua Barat Daya', 'Papua Pegunungan',
                                            'Papua Selatan', 'Papua Tengah', 'Riau', 'Sulawesi Barat',
                                            'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara',
                                            'Sulawesi Utara', 'Sumatera Barat', 'Sumatera Selatan',
                                            'Sumatera Utara',
                                        ] as $provinceName)
                                            <option value="{{ $provinceName }}" @selected($selectedProvince === $provinceName)>{{ $provinceName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="city" class="form-label">Kota/Kabupaten</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ old('city', $profile['city'] ?? '') }}"
                                        placeholder="Contoh: Kota Bandung" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="district" class="form-label">Kecamatan</label>
                                    <input type="text" class="form-control" id="district" name="district"
                                        value="{{ old('district', $profile['district'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="postal_code" class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                                        value="{{ old('postal_code', $profile['postal_code'] ?? '') }}" required>
                                </div>
                            </div>
                            <div class="form-section">
                                <label for="shipping_notes" class="form-label">Catatan Pengiriman (Opsional)</label>
                                <textarea class="form-control" id="shipping_notes" name="shipping_notes" rows="2">{{ old('shipping_notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Card -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h3 class="checkout-card-title">
                                <i class="fas fa-credit-card me-2"></i>Metode Pembayaran
                            </h3>
                        </div>
                        <div class="checkout-card-body">
                            {{-- #12: QRIS & E-Wallet ditambahkan sebagai simulasi (lihat
                                 CheckoutController::process()) — keduanya langsung tercatat
                                 lunas begitu pesanan dibuat, tidak ada payment gateway
                                 sungguhan yang terpasang. Kartu Kredit sengaja tidak
                                 ditawarkan (lihat catatan di Order::PAYMENT_METHODS). --}}
                            @php $selectedPaymentMethod = old('payment_method', 'bank_transfer'); @endphp
                            <div class="payment-method-options">
                                <label class="payment-method-option" for="payment-bank_transfer">
                                    <input type="radio" name="payment_method" id="payment-bank_transfer"
                                        value="bank_transfer"
                                        {{ $selectedPaymentMethod === 'bank_transfer' ? 'checked' : '' }}>
                                    <span class="payment-method-option-label">
                                        <i class="fas fa-university me-1"></i> Transfer Bank
                                    </span>
                                </label>
                                <label class="payment-method-option" for="payment-qris">
                                    <input type="radio" name="payment_method" id="payment-qris" value="qris"
                                        {{ $selectedPaymentMethod === 'qris' ? 'checked' : '' }}>
                                    <span class="payment-method-option-label">
                                        <i class="fas fa-qrcode me-1"></i> QRIS
                                    </span>
                                </label>
                                <label class="payment-method-option" for="payment-ewallet">
                                    <input type="radio" name="payment_method" id="payment-ewallet" value="ewallet"
                                        {{ $selectedPaymentMethod === 'ewallet' ? 'checked' : '' }}>
                                    <span class="payment-method-option-label">
                                        <i class="fas fa-wallet me-1"></i> E-Wallet
                                    </span>
                                </label>
                            </div>

                            <div class="payment-method-details" id="payment-details-bank_transfer"
                                style="{{ $selectedPaymentMethod === 'bank_transfer' ? '' : 'display: none;' }}">
                                <div class="alert alert-info mb-0">
                                    <h5 class="alert-heading"><i class="fas fa-university me-2"></i>Transfer Bank
                                    </h5>
                                    <p>Silakan transfer ke rekening berikut:</p>
                                    <p><strong>{{ $company->bank_name }}</strong><br>
                                        No. Rekening: {{ $company->bank_account_number }}<br>
                                        Atas Nama: {{ $company->bank_account_holder }}</p>
                                    <p class="mb-0">Konfirmasi pembayaran akan diproses dalam 1x24 jam kerja setelah
                                        Anda mengunggah bukti transfer.</p>
                                </div>
                            </div>

                            <div class="payment-method-details" id="payment-details-qris"
                                style="{{ $selectedPaymentMethod === 'qris' ? '' : 'display: none;' }}">
                                <div class="alert alert-info mb-0">
                                    <h5 class="alert-heading"><i class="fas fa-qrcode me-2"></i>QRIS</h5>
                                    <div class="qris-code-box">
                                        <svg class="qris-simulation-svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                                role="img" aria-label="Kode QRIS simulasi (bukan kode QR sungguhan yang bisa dipindai)">
                                <rect x="0" y="0" width="200" height="200" fill="#ffffff" rx="10"/>
                            <rect x="24" y="24" width="56" height="56" fill="#1f2937"/>
                            <rect x="32" y="32" width="40" height="40" fill="#ffffff"/>
                            <rect x="40" y="40" width="24" height="24" fill="#1f2937"/>
                            <rect x="120" y="24" width="56" height="56" fill="#1f2937"/>
                            <rect x="128" y="32" width="40" height="40" fill="#ffffff"/>
                            <rect x="136" y="40" width="24" height="24" fill="#1f2937"/>
                            <rect x="24" y="120" width="56" height="56" fill="#1f2937"/>
                            <rect x="32" y="128" width="40" height="40" fill="#ffffff"/>
                            <rect x="40" y="136" width="24" height="24" fill="#1f2937"/>
                            <rect x="88" y="72" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="72" width="8" height="8" fill="#1f2937"/>
                            <rect x="72" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="72" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="24" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="24" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="40" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="48" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="48" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="56" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="64" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="64" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="64" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="72" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="80" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="88" y="48" width="8" height="8" fill="#1f2937"/>
                            <rect x="88" y="64" width="8" height="8" fill="#1f2937"/>
                            <rect x="88" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="88" y="112" width="8" height="8" fill="#1f2937"/>
                            <rect x="88" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="88" y="160" width="8" height="8" fill="#1f2937"/>
                            <rect x="88" y="168" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="32" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="40" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="56" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="72" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="112" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="120" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="128" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="152" width="8" height="8" fill="#1f2937"/>
                            <rect x="96" y="168" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="32" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="40" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="48" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="56" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="120" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="152" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="160" width="8" height="8" fill="#1f2937"/>
                            <rect x="104" y="168" width="8" height="8" fill="#1f2937"/>
                            <rect x="112" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="112" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="112" y="168" width="8" height="8" fill="#1f2937"/>
                            <rect x="120" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="120" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="120" y="136" width="8" height="8" fill="#1f2937"/>
                            <rect x="120" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="120" y="152" width="8" height="8" fill="#1f2937"/>
                            <rect x="120" y="160" width="8" height="8" fill="#1f2937"/>
                            <rect x="120" y="168" width="8" height="8" fill="#1f2937"/>
                            <rect x="128" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="128" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="128" y="128" width="8" height="8" fill="#1f2937"/>
                            <rect x="128" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="128" y="152" width="8" height="8" fill="#1f2937"/>
                            <rect x="128" y="160" width="8" height="8" fill="#1f2937"/>
                            <rect x="128" y="168" width="8" height="8" fill="#1f2937"/>
                            <rect x="136" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="136" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="136" y="136" width="8" height="8" fill="#1f2937"/>
                            <rect x="136" y="168" width="8" height="8" fill="#1f2937"/>
                            <rect x="144" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="144" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="144" y="152" width="8" height="8" fill="#1f2937"/>
                            <rect x="152" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="152" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="152" y="112" width="8" height="8" fill="#1f2937"/>
                            <rect x="152" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="152" y="152" width="8" height="8" fill="#1f2937"/>
                            <rect x="152" y="160" width="8" height="8" fill="#1f2937"/>
                            <rect x="160" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="160" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="160" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="88" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="96" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="104" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="120" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="128" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="136" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="144" width="8" height="8" fill="#1f2937"/>
                            <rect x="168" y="168" width="8" height="8" fill="#1f2937"/>
                            </svg>
                                        <span class="qris-simulation-label">(Simulasi — bukan kode QR sungguhan)</span>
                                    </div>
                                    <p class="mb-0">Pembayaran QRIS pada demo ini disimulasikan <strong>langsung
                                            lunas</strong> begitu pesanan dibuat — tidak ada pemindaian sungguhan
                                        yang diperlukan.</p>
                                </div>
                            </div>

                            <div class="payment-method-details" id="payment-details-ewallet"
                                style="{{ $selectedPaymentMethod === 'ewallet' ? '' : 'display: none;' }}">
                                <div class="alert alert-info mb-0">
                                    <h5 class="alert-heading"><i class="fas fa-wallet me-2"></i>E-Wallet</h5>
                                    <p class="mb-0">Pembayaran E-Wallet pada demo ini disimulasikan <strong>langsung
                                            lunas</strong> begitu pesanan dibuat — tidak ada redirect ke aplikasi
                                        e-wallet sungguhan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="col-lg-4">
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h3 class="checkout-card-title">
                                <i class="fas fa-shopping-basket me-2"></i>Ringkasan Pesanan
                            </h3>
                        </div>
                        <div class="checkout-card-body">
                            <!-- Order Items List -->
                            <div class="order-items-list">
                                @foreach ($cartItems as $id => $item)
                                    <div class="order-item">
                                        <img src="{{ \App\Helpers\ImageHelper::url($item['image']) }}" alt="{{ $item['name'] }}"
                                            class="order-item-image">
                                        <div class="order-item-details">
                                            <div class="order-item-name">{{ $item['name'] }}</div>
                                            <div class="order-item-price">
                                                {{ \App\Helpers\CurrencyHelper::formatRupiah($item['price']) }}
                                            </div>
                                            <div class="order-item-quantity">x{{ $item['quantity'] }}</div>
                                        </div>
                                        <div class="order-item-total">
                                            {{ \App\Helpers\CurrencyHelper::formatRupiah($item['price'] * $item['quantity']) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Order Calculations -->
                            <div class="order-summary-calculations mt-4 pt-4 border-top">
                                <div class="order-summary-line">
                                    <span class="order-summary-label">Subtotal</span>
                                    <span class="order-summary-value">{{ \App\Helpers\CurrencyHelper::formatRupiah($total) }}</span>
                                </div>
                                <div class="order-summary-line">
                                    <span class="order-summary-label">Pengiriman</span>
                                    <span class="order-summary-value">
                                        {{ \App\Helpers\CurrencyHelper::formatRupiah($shipping_fee ?? 0) }}</span>
                                </div>
                                <div class="order-summary-line">
                                    <span class="order-summary-label">Pajak (11%)</span>
                                    <span class="order-summary-value">
                                        {{ \App\Helpers\CurrencyHelper::formatRupiah($tax ?? $total * 0.11) }}</span>
                                </div>
                                <div class="order-summary-line mt-4 pt-3 border-top">
                                    <span class="order-summary-label order-total">Total</span>
                                    <span class="order-summary-value order-total">
                                        {{ \App\Helpers\CurrencyHelper::formatRupiah($total + ($shipping_fee ?? 0) + ($tax ?? $total * 0.11)) }}</span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <div class="mt-4">
                                <button type="submit" class="btn-cta btn-cta-lg btn-checkout">
                                    <i class="fas fa-check-circle me-2"></i>Proses Pesanan
                                </button>
                                <a href="{{ route('cart.show') }}" class="btn-back mt-3 w-100 justify-content-center">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Keranjang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent double-submit: a slow connection or an impatient
            // double click can otherwise fire two separate checkout
            // requests for the same cart, creating two orders.
            const checkoutForm = document.querySelector('form[action="{{ route('checkout.process') }}"]');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function() {
                    const submitBtn = checkoutForm.querySelector('.btn-checkout');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                    }
                });
            }
        });
    </script>

    <script>
        // #11: toggle isi/kosongkan field pengiriman dari data profil.
        // profileData null kalau tamu ATAU user login yang belum simpan
        // profil (lihat blok checkbox "Gunakan data profil saya" di atas)
        // — JS di bawah no-op dengan aman di kedua kasus itu karena
        // checkbox-nya juga tidak ada.
        document.addEventListener('DOMContentLoaded', function() {
            const profileData = @json($profile);
            const useProfileCheckbox = document.getElementById('use-profile-data');
            const toggleFieldIds = ['phone', 'company', 'address', 'province', 'city', 'district', 'postal_code'];

            if (useProfileCheckbox && profileData) {
                useProfileCheckbox.addEventListener('change', function() {
                    toggleFieldIds.forEach(function(fieldId) {
                        const field = document.getElementById(fieldId);
                        if (!field) return;
                        field.value = useProfileCheckbox.checked ? (profileData[fieldId] || '') : '';
                    });
                });
            }
        });
    </script>

    <script>
        // #12: tampilkan blok info sesuai metode pembayaran yang dipilih.
        // Tetap berfungsi tanpa JS: style awal tiap panel sudah dihitung di
        // server (lihat $selectedPaymentMethod di atas), JS ini cuma
        // menambahkan interaktivitas toggle saat pilihan diganti.
        document.addEventListener('DOMContentLoaded', function() {
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            const detailPanels = document.querySelectorAll('.payment-method-details');
            const optionLabels = document.querySelectorAll('.payment-method-option');

            function updatePaymentDetails() {
                const selected = document.querySelector('input[name="payment_method"]:checked');
                if (!selected) return;

                detailPanels.forEach(function(panel) {
                    panel.style.display = panel.id === 'payment-details-' + selected.value ? 'block' : 'none';
                });

                optionLabels.forEach(function(option) {
                    option.classList.toggle('selected', option.contains(selected));
                });
            }

            paymentRadios.forEach(function(radio) {
                radio.addEventListener('change', updatePaymentDetails);
            });

            updatePaymentDetails();
        });
    </script>
@endpush
