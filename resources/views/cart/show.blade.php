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

        .scroll-top-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgb(var(--yellow-primary));
            color: rgb(0, 0, 0);
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .scroll-top-btn.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .scroll-top-btn:hover {
            background-color: rgb(var(--yellow-dark));
            transform: translateY(-5px);
        }

        .scroll-top-btn i {
            font-size: 1.2rem;
        }

        /* Cart Section */
        .cart-section {
            padding: 3rem 0;
            background-color: rgb(var(--gray-100));
            min-height: calc(100vh - 200px);
        }

        .cart-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
            color: rgb(var(--black));
        }

        .cart-separator {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgb(var(--yellow-primary)) 50%, transparent 100%);
            width: 150px;
            margin: 0 auto 3rem;
        }

        .cart-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            border: none;
        }

        .cart-table {
            margin-bottom: 0;
        }

        .cart-table th {
            border-top: none;
            border-bottom: 2px solid rgb(var(--gray-200));
            font-weight: 600;
            color: rgb(var(--gray-800));
            padding: 1.5rem 1rem;
        }

        .cart-table td {
            padding: 1.5rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid rgb(var(--gray-200));
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .cart-item-image:hover {
            transform: scale(1.05);
        }

        .cart-item-name {
            font-weight: 500;
            color: rgb(var(--black));
            transition: color 0.3s ease;
        }

        .cart-item-name:hover {
            color: rgb(var(--yellow-dark));
        }

        .cart-quantity-input {
            width: 100px;
            border-radius: 50px;
            border: 2px solid rgb(var(--gray-300));
            padding: 0.5rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .cart-quantity-input:focus {
            border-color: rgb(var(--yellow-primary));
            box-shadow: 0 0 0 0.2rem rgba(var(--yellow-primary), 0.25);
        }

        .cart-update-btn {
            background: white;
            border: 2px solid rgb(var(--gray-300));
            color: rgb(var(--gray-800));
            border-radius: 50px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .cart-update-btn:hover {
            background: rgb(var(--gray-200));
            border-color: rgb(var(--gray-400));
        }

        .cart-price,
        .cart-total {
            font-weight: 600;
            color: rgb(var(--gray-800));
        }

        .cart-item-total {
            font-weight: 700;
            color: rgb(var(--yellow-dark));
        }

        .cart-remove-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgb(var(--gray-100));
            color: #dc3545;
            border: none;
            transition: all 0.3s ease;
        }

        .cart-remove-btn:hover {
            background: #dc3545;
            color: white;
            transform: rotate(90deg);
        }

        .cart-footer {
            background: rgb(var(--gray-100));
            padding: 1.5rem;
            border-radius: 0 0 20px 20px;
        }

        .cart-total-row {
            font-size: 1.25rem;
            color: rgb(var(--black));
        }

        .cart-total-amount {
            font-weight: 700;
            color: rgb(var(--yellow-dark));
            font-size: 1.5rem;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .continue-shopping-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.875rem 2rem;
            background: white;
            color: rgb(var(--black));
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid rgb(var(--gray-300));
        }

        .continue-shopping-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: rgb(var(--gray-600));
            color: rgb(var(--black));
        }

        .checkout-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.875rem 2rem;
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(var(--yellow-primary), 0.3);
            position: relative;
            overflow: hidden;
            border: none;
        }

        .checkout-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgb(var(--yellow-primary)), rgb(var(--yellow-dark)));
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .checkout-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .checkout-btn:hover::before {
            opacity: 1;
        }

        .btn-text {
            position: relative;
            z-index: 1;
            margin-left: 0.5rem;
        }

        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        /* Empty Cart Styles */
        .empty-cart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .empty-cart-icon {
            width: 60px;
            height: 60px;
            background: rgb(var(--gray-100));
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 2rem;
            animation: pulseIcon 2s infinite;
        }

        @keyframes pulseIcon {
            0% {
                transform: scale(1);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
        }

        .empty-cart-icon i {
            font-size: 3rem;
            color: rgb(var(--gray-600));
        }

        .empty-cart-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgb(var(--gray-800));
        }

        .empty-cart-text {
            font-size: 1.1rem;
            color: rgb(var(--gray-600));
            margin-bottom: 2rem;
            text-align: center;
        }

        .start-shopping-btn {
            display: inline-flex;
            align-items: center;
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(var(--yellow-primary), 0.3);
            position: relative;
            overflow: hidden;
        }

        .start-shopping-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgb(var(--yellow-primary)), rgb(var(--yellow-dark)));
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .start-shopping-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .start-shopping-btn:hover::before {
            opacity: 1;
        }

        .start-shopping-text {
            position: relative;
            z-index: 1;
        }

        /* Alert Styles */
        .alert-elegant {
            border: none;
            border-radius: 15px;
            padding: 1.25rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .alert-elegant::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .alert-elegant.alert-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .alert-elegant.alert-success::before {
            background-color: #198754;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .cart-section {
                padding: 80px 0;
            }

            .cart-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 768px) {
            .cart-section {
                padding: 60px 0;
            }

            .cart-title {
                font-size: 2rem;
            }

            .cart-item-image {
                width: 80px;
                height: 80px;
            }

            .cart-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .continue-shopping-btn,
            .checkout-btn {
                width: 100%;
                justify-content: center;
            }

            .empty-cart-title {
                font-size: 1.75rem;
            }
        }

        .history-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.875rem 2rem;
            background: white;
            color: rgb(var(--yellow-dark));
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid rgb(var(--yellow-primary));
        }

        .history-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            background-color: rgb(var(--yellow-light));
            border-color: rgb(var(--yellow-dark));
            color: rgb(var(--black));
        }
    </style>
@endpush

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
                                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}"
                                                    class="cart-item-image me-3">
                                                <div class="cart-item-name">{{ $item['name'] }}</div>
                                            </div>
                                        </td>
                                        <td class="cart-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                        <td>
                                            <form action="{{ route('cart.update', $item['pivot_id']) }}" method="POST"
                                                class="d-flex align-items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                                    min="1" max="{{ $item['stock'] }}"
                                                    class="form-control cart-quantity-input">
                                                <button type="submit" class="btn cart-update-btn ms-2">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="cart-item-total">Rp
                                            {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                        <td>
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
                                    <span class="cart-total-amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
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
                        {{-- Tombol Riwayat Pesanan (Hanya Tampil Jika ada Pesanan) --}}
                        @if ($hasOrders)
                            <a href="{{ route('orders.index') }}" class="history-btn">
                                <div class="icon-container">
                                    <i class="fas fa-history"></i>
                                </div>
                                <span class="btn-text">Riwayat Pesanan</span>
                            </a>
                        @endif

                        {{-- Tombol Checkout --}}
                        <a href="{{ route('checkout.index') }}" class="checkout-btn">
                            <div class="icon-container">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <span class="btn-text">Checkout</span>
                        </a>
                    </div>
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
                        <p class="my-3">atau cek</p>
                        @if ($hasOrders)
                            <a href="{{ route('orders.index') }}" class="history-btn">
                                <div class="icon-container">
                                    <i class="fas fa-history"></i>
                                </div>
                                <span class="btn-text">Riwayat Pesanan</span>
                            </a>
                        @endif
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
@endpush
