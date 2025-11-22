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

        .orders-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .orders-card-header {
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-bottom: 1px solid rgb(var(--gray-200));
        }

        .orders-card-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0;
            color: rgb(var(--gray-800));
        }

        .orders-card-body {
            padding: 2rem;
        }

        .order-table {
            width: 100%;
        }

        .order-table th {
            padding: 1rem;
            background-color: rgb(var(--gray-100));
            font-weight: 600;
            color: rgb(var(--gray-800));
            text-align: left;
        }

        .order-table td {
            padding: 1rem;
            border-bottom: 1px solid rgb(var(--gray-200));
            vertical-align: middle;
        }

        .order-table tr:last-child td {
            border-bottom: none;
        }

        .order-status {
            display: inline-block;
            padding: 0.35rem 0.65rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 50px;
            text-align: center;
        }

        .btn-order {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(45deg, rgb(var(--yellow-dark)), rgb(var(--yellow-primary)));
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 10px rgba(var(--yellow-primary), 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-order:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .empty-orders {
            text-align: center;
            padding: 3rem 0;
        }

        .empty-orders-icon {
            font-size: 4rem;
            color: rgb(var(--gray-300));
            margin-bottom: 1rem;
        }

        .empty-orders-text {
            font-size: 1.25rem;
            color: rgb(var(--gray-600));
            margin-bottom: 2rem;
        }

        .btn-shop-now {
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
        }

        .btn-shop-now:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .pagination {
            margin-top: 2rem;
            justify-content: center;
        }

        .page-item:first-child .page-link {
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
        }

        .page-item:last-child .page-link {
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
        }

        .page-link {
            color: rgb(var(--gray-800));
            border: 1px solid rgb(var(--gray-200));
            padding: 0.5rem 1rem;
        }

        .page-item.active .page-link {
            background-color: rgb(var(--yellow-primary));
            border-color: rgb(var(--yellow-primary));
            color: white;
        }

        @media (max-width: 768px) {
            .section-title-elegant {
                font-size: 2rem;
            }

            .orders-card-body {
                padding: 1rem;
            }

            .order-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .order-table th,
            .order-table td {
                padding: 0.75rem;
            }
        }
    </style>
@endpush

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
                                            <a href="{{ route('orders.show', $order) }}" class="btn-order">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="empty-orders">
                        <div class="empty-orders-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h4>Belum Ada Pesanan</h4>
                        <p class="empty-orders-text">Anda belum memiliki pesanan. Mulai belanja sekarang!</p>
                        <a href="{{ route('products.index') }}" class="btn-shop-now">
                            <i class="fas fa-shopping-cart me-2"></i>Mulai Belanja
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection