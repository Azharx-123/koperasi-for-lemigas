@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/checkout-complete.css')
@endpush

@section('title', 'Pesanan Berhasil')

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
                @if ($order->payment_status === 'paid')
                    Kami telah mengirimkan detail pesanan ke email Anda. Pembayaran Anda sudah kami terima dan
                    pesanan akan segera kami proses.
                @else
                    Kami telah mengirimkan detail pesanan dan instruksi pembayaran ke email Anda.
                    Silakan selesaikan pembayaran untuk memproses pesanan Anda.
                @endif
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
                    <span class="order-value">{{ $order->formatted_total }}</span>
                </div>

                <div class="order-detail">
                    <span class="order-label">Status Pembayaran:</span>
                    <span class="order-value">{!! $order->payment_status_label !!}</span>
                </div>

                <div class="order-detail">
                    <span class="order-label">Metode Pembayaran:</span>
                    <span class="order-value">{{ $order->payment_method_label }}</span>
                </div>

                @if($order->payment_method == 'bank_transfer' && $order->payment_status == 'pending')
                <x-guest-order-expiry-notice :order="$order" />
                <div class="payment-instructions">
                    <h5>Instruksi Pembayaran:</h5>
                    <p>Silakan transfer sesuai total pesanan ke:</p>
                    <p>
                        <strong>{{ $company->bank_name }}</strong><br>
                        No. Rekening: {{ $company->bank_account_number }}<br>
                        Atas Nama: {{ $company->bank_account_holder }}<br>
                        Jumlah: {{ $order->formatted_total }}
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
