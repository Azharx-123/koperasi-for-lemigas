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

        .order-detail-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .order-detail-header {
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-bottom: 1px solid rgb(var(--gray-200));
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .order-detail-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0;
            color: rgb(var(--gray-800));
        }

        .order-status-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .order-detail-body {
            padding: 2rem;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .order-info-block {
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-radius: 10px;
        }

        .order-info-block h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgb(var(--gray-800));
            border-bottom: 2px solid rgb(var(--yellow-primary));
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .order-info-item {
            margin-bottom: 0.75rem;
        }

        .order-info-label {
            font-weight: 500;
            color: rgb(var(--gray-600));
            margin-bottom: 0.25rem;
        }

        .order-info-value {
            font-weight: 500;
            color: rgb(var(--gray-800));
        }

        .order-items-table {
            width: 100%;
            margin-top: 2rem;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .order-items-table th {
            background-color: rgb(var(--gray-100));
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: rgb(var(--gray-800));
        }

        .order-items-table td {
            padding: 1rem;
            border-bottom: 1px solid rgb(var(--gray-200));
            vertical-align: middle;
        }

        .order-items-table tr:last-child td {
            border-bottom: none;
        }

        .product-info {
            display: flex;
            align-items: center;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1rem;
        }

        .product-name {
            font-weight: 500;
        }

        .order-summary {
            margin-top: 2rem;
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-radius: 10px;
        }

        .order-summary-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgb(var(--gray-800));
            border-bottom: 2px solid rgb(var(--yellow-primary));
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .summary-label {
            font-weight: 500;
            color: rgb(var(--gray-600));
        }

        .summary-value {
            font-weight: 500;
            color: rgb(var(--gray-800));
        }

        .summary-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: rgb(var(--yellow-dark));
            border-top: 1px solid rgb(var(--gray-300));
            padding-top: 0.75rem;
            margin-top: 0.75rem;
        }

        .payment-proof {
            margin-top: 2rem;
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-radius: 10px;
        }

        .payment-proof-image {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .actions-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 15px rgba(var(--yellow-primary), 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .btn-secondary-action {
            background: white;
            color: rgb(var(--gray-800));
            border: 2px solid rgb(var(--gray-300));
            box-shadow: none;
        }

        .btn-secondary-action:hover {
            background: rgb(var(--gray-100));
            color: rgb(var(--gray-800));
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-danger-action {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-danger-action:hover {
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
        }

        @media (max-width: 768px) {
            .section-title-elegant {
                font-size: 2rem;
            }

            .order-detail-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-status-badges {
                margin-top: 1rem;
            }

            .order-detail-body {
                padding: 1rem;
            }

            .order-info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .order-items-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
@endpush

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
                                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="product-image">
                                            @else
                                                <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="product-name">
                                                {{ $item->product ? $item->product->name : 'Produk tidak tersedia' }}
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
                            <img src="{{ Storage::url($order->paymentConfirmation->proof_image) }}" alt="Bukti Pembayaran" class="payment-proof-image">
                        </div>
                    </div>
                @endif

                <!-- Order Actions -->
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