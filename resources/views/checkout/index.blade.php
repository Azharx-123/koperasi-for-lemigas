@extends('layouts.app')

@push('styles')
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

        /* Elegant Separator Style */
        .elegant-separator {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgb(var(--yellow-primary)) 50%, transparent 100%);
            width: 150px;
            margin: 2rem auto;
        }

        /* Section Styles */
        .section-title-elegant {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: rgb(var(--black));
            margin-bottom: 1rem;
            position: relative;
            text-align: center;
        }

        /* Checkout Card Styles */
        .checkout-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .checkout-card-header {
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-bottom: 1px solid rgb(var(--gray-200));
        }

        .checkout-card-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0;
            color: rgb(var(--gray-800));
        }

        .checkout-card-body {
            padding: 2rem;
        }

        /* Order Summary Styles */
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgb(var(--gray-200));
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 1.5rem;
        }

        .order-item-details {
            flex-grow: 1;
        }

        .order-item-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .order-item-price {
            color: rgb(var(--gray-600));
            font-size: 0.9rem;
        }

        .order-item-quantity {
            background-color: rgb(var(--yellow-primary));
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.9rem;
            margin: 0.5rem 0.5rem 0 0.5rem;
            text-align: center;
        }

        .order-item-total {
            font-weight: 600;
            color: rgb(var(--yellow-dark));
            font-size: 1.1rem;
            margin-left: 1rem;
        }

        /* Order Summary Totals */
        .order-summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .order-summary-label {
            font-weight: 500;
            color: rgb(var(--gray-600));
        }

        .order-summary-value {
            font-weight: 600;
        }

        .order-total {
            font-size: 1.25rem;
            font-weight: 700;
            color: rgb(var(--yellow-dark));
        }

        /* Form Styles */
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: rgb(var(--gray-800));
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid rgb(var(--gray-300));
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(var(--yellow-primary), 0.25);
            border-color: rgb(var(--yellow-primary));
        }

        .form-section {
            margin-bottom: 2rem;
        }

        /* Payment Method Styles */
        .payment-method-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .payment-method-item {
            flex: 1;
            min-width: 120px;
        }

        .payment-method-radio {
            display: none;
        }

        .payment-method-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 1rem;
            border: 2px solid rgb(var(--gray-200));
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method-radio:checked+.payment-method-label {
            border-color: rgb(var(--yellow-primary));
            background-color: rgba(var(--yellow-primary), 0.05);
        }

        .payment-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: rgb(var(--gray-600));
        }

        .payment-method-radio:checked+.payment-method-label .payment-icon {
            color: rgb(var(--yellow-dark));
        }

        .payment-label-text {
            font-weight: 500;
            text-align: center;
        }

        /* Button Styles */
        .btn-checkout {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 15px rgba(var(--yellow-primary), 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            width: 100%;
        }

        .btn-checkout:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 1rem 2rem;
            background-color: white;
            color: rgb(var(--gray-800));
            border: 2px solid rgb(var(--gray-300));
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: rgb(var(--gray-100));
            color: rgb(var(--gray-800));
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .section-title-elegant {
                font-size: 2rem;
            }

            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-item-image {
                margin-bottom: 1rem;
                margin-right: 0;
            }

            .order-item-total {
                margin-left: 0;
                margin-top: 0.5rem;
            }

            .checkout-card-body {
                padding: 1.5rem;
            }

            .payment-method-item {
                min-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <h2 class="section-title-elegant">Checkout</h2>
        <div class="elegant-separator"></div>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

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
                            <div class="row">
                                <div class="col-md-6 form-section">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ auth()->user()->name ?? '' }}" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ auth()->user()->email ?? '' }}" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="company" class="form-label">Perusahaan (Opsional)</label>
                                    <input type="text" class="form-control" id="company" name="company">
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
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-section">
                                    <label for="province" class="form-label">Provinsi</label>
                                    <select class="form-control" id="province" name="province" required>
                                        <option value="">Pilih Provinsi</option>
                                        <option value="DKI Jakarta">DKI Jakarta</option>
                                        <option value="Jawa Barat">Jawa Barat</option>
                                        <option value="Jawa Tengah">Jawa Tengah</option>
                                        <option value="Jawa Timur">Jawa Timur</option>
                                        <!-- Add more provinces as needed -->
                                    </select>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="city" class="form-label">Kota/Kabupaten</label>
                                    <select class="form-control" id="city" name="city" required>
                                        <option value="">Pilih Kota/Kabupaten</option>
                                        <!-- Cities will be loaded dynamically based on province -->
                                    </select>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="district" class="form-label">Kecamatan</label>
                                    <input type="text" class="form-control" id="district" name="district" required>
                                </div>
                                <div class="col-md-6 form-section">
                                    <label for="postal_code" class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                                </div>
                            </div>
                            <div class="form-section">
                                <label for="shipping_notes" class="form-label">Catatan Pengiriman (Opsional)</label>
                                <textarea class="form-control" id="shipping_notes" name="shipping_notes" rows="2"></textarea>
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
                            <div class="payment-method-selector">
                                <div class="payment-method-item">
                                    <input type="radio" class="payment-method-radio" id="payment_bank_transfer"
                                        name="payment_method" value="bank_transfer" checked>
                                    <label for="payment_bank_transfer" class="payment-method-label">
                                        <i class="fas fa-university payment-icon"></i>
                                        <span class="payment-label-text">Transfer Bank</span>
                                    </label>
                                </div>
                                <div class="payment-method-item">
                                    <input type="radio" class="payment-method-radio" id="payment_credit_card"
                                        name="payment_method" value="credit_card">
                                    <label for="payment_credit_card" class="payment-method-label">
                                        <i class="fas fa-credit-card payment-icon"></i>
                                        <span class="payment-label-text">Kartu Kredit</span>
                                    </label>
                                </div>
                                <div class="payment-method-item">
                                    <input type="radio" class="payment-method-radio" id="payment_ewallet"
                                        name="payment_method" value="ewallet">
                                    <label for="payment_ewallet" class="payment-method-label">
                                        <i class="fas fa-wallet payment-icon"></i>
                                        <span class="payment-label-text">E-Wallet</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Bank Transfer Details (shown by default) -->
                            <div id="bank_transfer_details" class="payment-details mt-4">
                                <div class="alert alert-info">
                                    <h5 class="alert-heading">Instruksi Pembayaran:</h5>
                                    <p>Silakan transfer ke rekening berikut:</p>
                                    <p><strong>Bank Mandiri</strong><br>
                                        No. Rekening: 1234567890<br>
                                        Atas Nama: LEMIGAS</p>
                                    <p>Konfirmasi pembayaran akan diproses dalam 1x24 jam kerja.</p>
                                </div>
                            </div>

                            <!-- Credit Card Details (hidden by default) -->
                            <div id="credit_card_details" class="payment-details mt-4" style="display: none;">
                                <div class="row">
                                    <div class="col-12 form-section">
                                        <label for="card_number" class="form-label">Nomor Kartu</label>
                                        <input type="text" class="form-control" id="card_number" name="card_number"
                                            placeholder="1234 5678 9012 3456">
                                    </div>
                                    <div class="col-md-6 form-section">
                                        <label for="card_expiry" class="form-label">Tanggal Kadaluarsa</label>
                                        <input type="text" class="form-control" id="card_expiry" name="card_expiry"
                                            placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6 form-section">
                                        <label for="card_cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="card_cvv" name="card_cvv"
                                            placeholder="123">
                                    </div>
                                    <div class="col-12 form-section">
                                        <label for="card_holder" class="form-label">Nama Pemegang Kartu</label>
                                        <input type="text" class="form-control" id="card_holder" name="card_holder">
                                    </div>
                                </div>
                            </div>

                            <!-- E-Wallet Details (hidden by default) -->
                            <div id="ewallet_details" class="payment-details mt-4" style="display: none;">
                                <div class="row">
                                    <div class="col-12 form-section">
                                        <label for="ewallet_type" class="form-label">Pilih E-Wallet</label>
                                        <select class="form-control" id="ewallet_type" name="ewallet_type">
                                            <option value="">Pilih E-Wallet</option>
                                            <option value="gopay">GoPay</option>
                                            <option value="ovo">OVO</option>
                                            <option value="dana">DANA</option>
                                            <option value="linkaja">LinkAja</option>
                                        </select>
                                    </div>
                                    <div class="col-12 form-section">
                                        <label for="phone_number" class="form-label">Nomor Telepon Terdaftar</label>
                                        <input type="tel" class="form-control" id="phone_number"
                                            name="phone_number">
                                    </div>
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
                                        <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}"
                                            class="order-item-image">
                                        <div class="order-item-details">
                                            <div class="order-item-name">{{ $item['name'] }}</div>
                                            <div class="order-item-price">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </div>
                                            <div class="order-item-quantity">x{{ $item['quantity'] }}</div>
                                        </div>
                                        <div class="order-item-total">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Order Calculations -->
                            <div class="order-summary-calculations mt-4 pt-4 border-top">
                                <div class="order-summary-line">
                                    <span class="order-summary-label">Subtotal</span>
                                    <span class="order-summary-value">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="order-summary-line">
                                    <span class="order-summary-label">Pengiriman</span>
                                    <span class="order-summary-value">Rp
                                        {{ number_format($shipping_fee ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="order-summary-line">
                                    <span class="order-summary-label">Pajak (11%)</span>
                                    <span class="order-summary-value">Rp
                                        {{ number_format($tax ?? $total * 0.11, 0, ',', '.') }}</span>
                                </div>
                                <div class="order-summary-line mt-4 pt-3 border-top">
                                    <span class="order-summary-label order-total">Total</span>
                                    <span class="order-summary-value order-total">Rp
                                        {{ number_format($total + ($shipping_fee ?? 0) + ($tax ?? $total * 0.11), 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <div class="mt-4">
                                <button type="submit" class="btn-checkout">
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
            // Payment method selection
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const bankTransferDetails = document.getElementById('bank_transfer_details');
            const creditCardDetails = document.getElementById('credit_card_details');
            const ewalletDetails = document.getElementById('ewallet_details');

            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    // Hide all payment details sections
                    bankTransferDetails.style.display = 'none';
                    creditCardDetails.style.display = 'none';
                    ewalletDetails.style.display = 'none';

                    // Show the selected payment details section
                    switch (this.value) {
                        case 'bank_transfer':
                            bankTransferDetails.style.display = 'block';
                            break;
                        case 'credit_card':
                            creditCardDetails.style.display = 'block';
                            break;
                        case 'ewallet':
                            ewalletDetails.style.display = 'block';
                            break;
                    }
                });
            });

            // Dynamic city selection based on province
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            provinceSelect.addEventListener('change', function() {
                // Clear current options
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';

                // Add cities based on selected province
                if (this.value === 'DKI Jakarta') {
                    const jakartaCities = ['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat',
                        'Jakarta Selatan', 'Jakarta Timur', 'Kepulauan Seribu'
                    ];
                    jakartaCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                } else if (this.value === 'Jawa Barat') {
                    const westJavaCities = ['Bandung', 'Bekasi', 'Bogor', 'Depok', 'Cimahi', 'Tasikmalaya',
                        'Cirebon'
                    ];
                    westJavaCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                } else if (this.value === 'Jawa Tengah') {
                    const centralJavaCities = ['Semarang', 'Solo', 'Magelang', 'Salatiga', 'Surakarta',
                        'Pekalongan', 'Tegal'
                    ];
                    centralJavaCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                } else if (this.value === 'Jawa Timur') {
                    const eastJavaCities = ['Surabaya', 'Malang', 'Kediri', 'Mojokerto', 'Madiun', 'Batu',
                        'Blitar', 'Pasuruan'
                    ];
                    eastJavaCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }
            });
        });
    </script>
@endpush
