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

        .section-title-elegant {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: rgb(var(--black));
            margin-bottom: 1rem;
            position: relative;
            text-align: center;
        }

        .completion-container {
            padding: 5rem 0;
            text-align: center;
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background-color: rgba(var(--yellow-primary), 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }

        .success-icon i {
            font-size: 7rem;
            color: rgb(var(--yellow-dark));
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(var(--yellow-primary), 0.4);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(var(--yellow-primary), 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(var(--yellow-primary), 0);
            }
        }

        .completion-message {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgb(var(--gray-800));
        }

        .completion-subtext {
            font-size: 1.1rem;
            color: rgb(var(--gray-600));
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .order-info-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto 3rem;
            text-align: left;
        }

        .order-info-title {
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 1px solid rgb(var(--gray-200));
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .order-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .order-label {
            font-weight: 500;
            color: rgb(var(--gray-600));
        }

        .order-value {
            font-weight: 600;
            color: rgb(var(--gray-800));
        }

        .payment-instructions {
            background-color: rgba(var(--yellow-primary), 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .payment-instructions h5 {
            font-weight: 600;
            color: rgb(var(--yellow-dark));
            margin-bottom: 1rem;
        }

        .btn-action {
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
            text-decoration: none;
            margin: 0 0.5rem 1rem;
        }

        .btn-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .btn-outline {
            background: white;
            color: rgb(var(--gray-800));
            border: 2px solid rgb(var(--gray-300));
        }

        .btn-outline:hover {
            color: rgb(var(--gray-800));
            background-color: rgb(var(--gray-100));
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .section-title-elegant {
                font-size: 2rem;
            }

            .completion-message {
                font-size: 1.25rem;
            }

            .completion-subtext {
                font-size: 1rem;
            }

            .success-icon {
                width: 100px;
                height: 100px;
            }

            .success-icon i {
                font-size: 3rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="completion-container">
            <div class="success-icon">
                <i class="fas fa-check-circle fs-1"></i>
            </div>
            
            <h2 class="section-title-elegant">Terima Kasih!</h2>
            <div class="elegant-separator"></div>
            
            <p class="completion-message">Pesanan Anda Berhasil Dibuat</p>
            <p class="completion-subtext">
                Kami telah mengirimkan detail pesanan dan instruksi pembayaran ke email Anda. 
                Silakan selesaikan pembayaran untuk memproses pesanan Anda.
            </p>
            
            <div class="order-info-card">
                <h3 class="order-info-title">Informasi Pesanan</h3>
                
                <div class="order-detail">
                    <span class="order-label">Nomor Pesanan:</span>
                    <span class="order-value">{{ $order->order_number }}</span>
                </div>
                
                <div class="order-detail">
                    <span class="order-label">Tanggal Pesanan:</span>
                    <span class="order-value">{{ $order->created_at->format('d F Y, H:i') }}</span>
                </div>
                
                <div class="order-detail">
                    <span class="order-label">Total Pesanan:</span>
                    <span class="order-value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                
                <div class="order-detail">
                    <span class="order-label">Status Pembayaran:</span>
                    <span class="order-value">
                        @if($order->payment_status == 'pending')
                            <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                        @elseif($order->payment_status == 'paid')
                            <span class="badge bg-success">Lunas</span>
                        @else
                            <span class="badge bg-danger">Gagal</span>
                        @endif
                    </span>
                </div>
                
                <div class="order-detail">
                    <span class="order-label">Metode Pembayaran:</span>
                    <span class="order-value">
                        @if($order->payment_method == 'bank_transfer')
                            Transfer Bank
                        @elseif($order->payment_method == 'credit_card')
                            Kartu Kredit
                        @elseif($order->payment_method == 'ewallet')
                            E-Wallet
                        @endif
                    </span>
                </div>
                
                @if($order->payment_method == 'bank_transfer' && $order->payment_status == 'pending')
                <div class="payment-instructions">
                    <h5>Instruksi Pembayaran:</h5>
                    <p>Silakan transfer sesuai total pesanan ke:</p>
                    <p>
                        <strong>Bank Mandiri</strong><br>
                        No. Rekening: 1234567890<br>
                        Atas Nama: LEMIGAS<br>
                        Jumlah: Rp {{ number_format($order->total, 0, ',', '.') }}
                    </p>
                    <p class="mb-0">
                        Setelah melakukan pembayaran, silakan konfirmasi melalui halaman konfirmasi pembayaran atau hubungi customer service kami.
                    </p>
                </div>
                @endif
            </div>
            
            <div>
                <a href="{{ route('orders.show', $order->id) }}" class="btn-action">
                    <i class="fas fa-file-alt me-2"></i>Detail Pesanan
                </a>
                <a href="{{ route('products.index') }}" class="btn-action btn-outline">
                    <i class="fas fa-shopping-bag me-2"></i>Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
@endsection