@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/orders-index.css')
@endpush

@section('title', 'Riwayat Pesanan')

@section('content')
    <div class="container py-5">
        <h2 class="section-title-elegant">Riwayat Pesanan</h2>
        <div class="elegant-separator"></div>

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

        <div class="orders-card">
            <div class="orders-card-header">
                <h3 class="orders-card-title">
                    <i class="fas fa-shopping-bag me-2"></i>Daftar Pesanan Anda
                </h3>
            </div>
            <div class="orders-card-body">
                @if(count($orders) > 0)
                    <div class="table-responsive">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                        <td>{{ $order->formatted_total }}</td>
                                        <td>{!! $order->status_label !!}</td>
                                        <td>{!! $order->payment_status_label !!}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn-cta btn-cta-sm">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($orders->hasPages())
                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-orders">
                        <div class="empty-orders-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h4>Belum Ada Pesanan</h4>
                        <p class="empty-orders-text">Anda belum memiliki pesanan. Mulai belanja sekarang!</p>
                        <a href="{{ route('products.index') }}" class="btn-cta btn-cta-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Mulai Belanja
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
