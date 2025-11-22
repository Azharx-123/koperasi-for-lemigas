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

        .payment-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .payment-card-header {
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-bottom: 1px solid rgb(var(--gray-200));
        }

        .payment-card-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0;
            color: rgb(var(--gray-800));
        }

        .payment-card-body {
            padding: 2rem;
        }

        .order-info {
            background-color: rgb(var(--gray-100));
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .order-info-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgb(var(--gray-800));
            border-bottom: 2px solid rgb(var(--yellow-primary));
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .order-detail-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .order-detail-label {
            font-weight: 500;
            color: rgb(var(--gray-600));
        }

        .order-detail-value {
            font-weight: 600;
            color: rgb(var(--yellow-dark));
        }

        .payment-instructions {
            background-color: rgba(var(--yellow-light), 0.15);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border-left: 4px solid rgb(var(--yellow-primary));
        }

        .instructions-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgb(var(--gray-800));
        }

        .bank-details {
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .bank-detail-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .bank-detail-label {
            font-weight: 500;
            color: rgb(var(--gray-600));
        }

        .bank-detail-value {
            font-weight: 600;
            color: rgb(var(--gray-800));
        }

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
            margin-bottom: 1.5rem;
        }

        .image-preview-container {
            width: 100%;
            height: 300px;
            background-color: rgb(var(--gray-100));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1rem;
            overflow: hidden;
            position: relative;
        }

        .image-preview {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .image-placeholder {
            font-size: 4rem;
            color: rgb(var(--gray-300));
        }

        .btn-submit {
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

        .btn-submit:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(var(--yellow-primary), 0.4);
            color: white;
        }

        .btn-cancel {
            display: inline-block;
            padding: 1rem 2rem;
            background: white;
            color: rgb(var(--gray-800));
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 50px;
            border: 2px solid rgb(var(--gray-300));
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            text-decoration: none;
            margin-top: 1rem;
        }

        .btn-cancel:hover {
            background-color: rgb(var(--gray-100));
            color: rgb(var(--gray-800));
        }

        @media (max-width: 768px) {
            .section-title-elegant {
                font-size: 2rem;
            }

            .payment-card-body {
                padding: 1rem;
            }

            .image-preview-container {
                height: 200px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <h2 class="section-title-elegant">Konfirmasi Pembayaran</h2>
        <div class="elegant-separator"></div>

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
                            <span class="bank-detail-value">Bank Mandiri</span>
                        </div>
                        <div class="bank-detail-line">
                            <span class="bank-detail-label">No. Rekening:</span>
                            <span class="bank-detail-value">1234567890</span>
                        </div>
                        <div class="bank-detail-line">
                            <span class="bank-detail-label">Atas Nama:</span>
                            <span class="bank-detail-value">LEMIGAS</span>
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