@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/orders-show.css')
@endpush

@section('title', 'Pesanan #' . $order->order_number)

@section('content')
    <div class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-dark">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pesanan
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="order-detail-card">
            <div class="order-detail-header">
                <h3 class="order-detail-title">
                    <i class="fas fa-file-invoice me-2"></i>Detail Pesanan #{{ $order->order_number }}
                </h3>
                <div class="order-status-badges">
                    <div>{!! $order->status_label !!}</div>
                    <div>{!! $order->payment_status_label !!}</div>
                </div>
            </div>
            <div class="order-detail-body">
                <!-- Order Information Grid -->
                <div class="order-info-grid">
                    <div class="order-info-block">
                        <h4>Informasi Pesanan</h4>
                        <div class="order-info-item">
                            <div class="order-info-label">Nomor Pesanan</div>
                            <div class="order-info-value">{{ $order->order_number }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Tanggal Pesanan</div>
                            <div class="order-info-value">{{ $order->created_at->format('d F Y, H:i') }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Metode Pembayaran</div>
                            <div class="order-info-value">{{ $order->payment_method_label }}</div>
                        </div>
                        @if($order->tracking_number)
                            <div class="order-info-item">
                                <div class="order-info-label">Nomor Resi</div>
                                <div class="order-info-value">{{ $order->tracking_number }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="order-info-block">
                        <h4>Informasi Pengiriman</h4>
                        <div class="order-info-item">
                            <div class="order-info-label">Nama Penerima</div>
                            <div class="order-info-value">{{ $order->name }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Alamat</div>
                            <div class="order-info-value">{{ $order->address }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Kota/Kabupaten</div>
                            <div class="order-info-value">{{ $order->city }}, {{ $order->province }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Kode Pos</div>
                            <div class="order-info-value">{{ $order->postal_code }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Telepon</div>
                            <div class="order-info-value">{{ $order->phone }}</div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <h4 class="mt-4 mb-3">Item Pesanan</h4>
                <div class="table-responsive">
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ \App\Helpers\ImageHelper::url($item->product->image) }}" alt="{{ $item->display_name }}" class="product-image">
                                            @else
                                                <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="product-name">
                                                {{ $item->display_name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->formatted_price }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->formatted_total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <h4 class="order-summary-title">Ringkasan Pesanan</h4>
                    <div class="summary-line">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value">{{ $order->formatted_subtotal }}</span>
                    </div>
                    <div class="summary-line">
                        <span class="summary-label">Biaya Pengiriman</span>
                        <span class="summary-value">{{ $order->formatted_shipping }}</span>
                    </div>
                    <div class="summary-line">
                        <span class="summary-label">Pajak (11%)</span>
                        <span class="summary-value">{{ $order->formatted_tax }}</span>
                    </div>
                    <div class="summary-line summary-total">
                        <span class="summary-label">Total</span>
                        <span class="summary-value">{{ $order->formatted_total }}</span>
                    </div>
                </div>

                <!-- Payment Proof (if exists) -->
                @if($order->paymentConfirmation)
                    <div class="payment-proof">
                        <h4 class="order-summary-title">Bukti Pembayaran</h4>
                        <div class="order-info-item">
                            <div class="order-info-label">Bank</div>
                            <div class="order-info-value">{{ $order->paymentConfirmation->bank_name }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Atas Nama</div>
                            <div class="order-info-value">{{ $order->paymentConfirmation->account_name }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Tanggal Transfer</div>
                            <div class="order-info-value">{{ $order->paymentConfirmation->transfer_date->format('d F Y') }}</div>
                        </div>
                        <div class="order-info-item">
                            <div class="order-info-label">Jumlah</div>
                            <div class="order-info-value">{{ $order->paymentConfirmation->formatted_amount }}</div>
                        </div>
                        @if($order->paymentConfirmation->notes)
                            <div class="order-info-item">
                                <div class="order-info-label">Catatan</div>
                                <div class="order-info-value">{{ $order->paymentConfirmation->notes }}</div>
                            </div>
                        @endif
                        <div class="text-center">
                            <img src="{{ \App\Helpers\ImageHelper::url($order->paymentConfirmation->proof_image) }}" alt="Bukti Pembayaran" class="payment-proof-image">
                        </div>
                    </div>
                @endif

                <!-- Order Actions -->
                @if($order->payment_method == 'bank_transfer' && $order->payment_status == 'pending' && !$order->paymentConfirmation)
                    <x-guest-order-expiry-notice :order="$order" />
                @endif
                <div class="actions-container">
                    @if($order->payment_method == 'bank_transfer' && $order->payment_status == 'pending' && !$order->paymentConfirmation)
                        <a href="{{ route('orders.payment.confirmation', $order) }}" class="btn-action">
                            <i class="fas fa-money-check-alt me-2"></i>Konfirmasi Pembayaran
                        </a>
                    @endif

                    @if(in_array($order->status, ['pending', 'processing']) && $order->payment_status != 'paid')
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                            @csrf
                            <button type="submit" class="btn-action btn-danger-action">
                                <i class="fas fa-times-circle me-2"></i>Batalkan Pesanan
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('orders.index') }}" class="btn-action btn-secondary-action">
                        <i class="fas fa-list me-2"></i>Daftar Pesanan
                    </a>

                    <a href="{{ route('products.index') }}" class="btn-action">
                        <i class="fas fa-shopping-cart me-2"></i>Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
