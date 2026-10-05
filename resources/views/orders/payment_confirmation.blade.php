@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/orders-payment-confirmation.css')
@endpush

@section('title', 'Konfirmasi Pembayaran')

@section('content')
    <div class="container py-5">
        <h2 class="section-title-elegant">Konfirmasi Pembayaran</h2>
        <div class="elegant-separator"></div>

        <x-guest-order-expiry-notice :order="$order" />

        <div class="payment-card">
            <div class="payment-card-header">
                <h3 class="payment-card-title">
                    <i class="fas fa-money-check-alt me-2"></i>Konfirmasi Pembayaran #{{ $order->order_number }}
                </h3>
            </div>
            <div class="payment-card-body">
                <!-- Order Summary -->
                <div class="order-info">
                    <h4 class="order-info-title">Informasi Pesanan</h4>
                    <div class="order-detail-line">
                        <span class="order-detail-label">Nomor Pesanan:</span>
                        <span class="order-detail-value">{{ $order->order_number }}</span>
                    </div>
                    <div class="order-detail-line">
                        <span class="order-detail-label">Tanggal Pesanan:</span>
                        <span class="order-detail-value">{{ $order->created_at->format('d F Y, H:i') }}</span>
                    </div>
                    <div class="order-detail-line">
                        <span class="order-detail-label">Total Pembayaran:</span>
                        <span class="order-detail-value">{{ $order->formatted_total }}</span>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="payment-instructions">
                    <h4 class="instructions-title">Instruksi Pembayaran</h4>
                    <p>Silakan transfer sesuai total pesanan ke rekening berikut:</p>

                    <div class="bank-details">
                        <div class="bank-detail-line">
                            <span class="bank-detail-label">Bank:</span>
                            <span class="bank-detail-value">{{ $company->bank_name }}</span>
                        </div>
                        <div class="bank-detail-line">
                            <span class="bank-detail-label">No. Rekening:</span>
                            <span class="bank-detail-value">{{ $company->bank_account_number }}</span>
                        </div>
                        <div class="bank-detail-line">
                            <span class="bank-detail-label">Atas Nama:</span>
                            <span class="bank-detail-value">{{ $company->bank_account_holder }}</span>
                        </div>
                        <div class="bank-detail-line">
                            <span class="bank-detail-label">Jumlah:</span>
                            <span class="bank-detail-value">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    <p class="mb-0">Setelah melakukan pembayaran, silakan isi form konfirmasi pembayaran di bawah ini.</p>
                </div>

                <!-- Confirmation Form -->
                <form action="{{ route('orders.payment.process', $order) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-section">
                            <label for="bank_name" class="form-label">Bank Pengirim</label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-section">
                            <label for="account_name" class="form-label">Nama Pengirim</label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-section">
                            <label for="amount" class="form-label">Jumlah Transfer</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ $order->total }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-section">
                            <label for="transfer_date" class="form-label">Tanggal Transfer</label>
                            <input type="date" class="form-control @error('transfer_date') is-invalid @enderror" id="transfer_date" name="transfer_date" value="{{ date('Y-m-d') }}" required>
                            @error('transfer_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 form-section">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2"></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 form-section">
                            <label for="proof_image" class="form-label">Bukti Transfer</label>
                            <input type="file" class="form-control @error('proof_image') is-invalid @enderror" id="proof_image" name="proof_image" accept="image/*" required>
                            @error('proof_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="image-preview-container" id="imagePreviewContainer">
                                <div class="image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check-circle me-2"></i>Kirim Konfirmasi Pembayaran
                        </button>
                        <a href="{{ route('orders.show', $order) }}" class="btn-cancel">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const proofImage = document.getElementById('proof_image');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePlaceholder = imagePreviewContainer.querySelector('.image-placeholder');

        proofImage.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Remove placeholder
                    if (imagePlaceholder) {
                        imagePlaceholder.style.display = 'none';
                    }

                    // Create or replace image
                    let imagePreview = imagePreviewContainer.querySelector('.image-preview');
                    if (!imagePreview) {
                        imagePreview = document.createElement('img');
                        imagePreview.classList.add('image-preview');
                        imagePreviewContainer.appendChild(imagePreview);
                    }

                    imagePreview.src = e.target.result;
                }

                reader.readAsDataURL(e.target.files[0]);
            }
        });
    });
</script>
@endpush
