@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/profile-edit.css')
@endpush

@section('title', 'Edit Profil')

@section('content')
    <div class="container profile-container my-5">
        <div class="profile-header">
            <h2>Profil Saya</h2>
            <p>Kelola informasi profil dan pengaturan akun Anda</p>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mb-4">
                Profil berhasil diperbarui.
            </div>
        @endif

        <div class="card">
            <div class="card-body p-4">
                <h4 class="section-title">Informasi Profil</h4>

                <div class="avatar-section">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=FFD700&color=212529&size=100"
                        alt="{{ $user->name }}" class="avatar">
                    <div class="avatar-info">
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        <small>Bergabung: {{ $user->created_at->format('d M Y') }}</small>
                    </div>
                </div>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <form method="post" action="{{ route('profile.update') }}" id="profile-form">
                    @csrf
                    @method('patch')

                    <div class="mb-4">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-muted mb-2">
                                    Email Anda belum terverifikasi.
                                </p>
                                <button form="send-verification" class="btn btn-secondary btn-sm">
                                    Kirim Email Verifikasi
                                </button>
                            </div>
                        @endif
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <h5 class="mb-1">Data Pengiriman</h5>
                        <p class="text-muted small mb-0">
                            Opsional — kalau diisi, data ini otomatis mengisi form checkout supaya Anda tidak perlu
                            mengetik ulang setiap belanja.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label">Nomor HP</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="company" class="form-label">Perusahaan (Opsional)</label>
                        <input type="text" class="form-control @error('company') is-invalid @enderror" id="company"
                            name="company" value="{{ old('company', $user->company) }}" autocomplete="organization">
                        @error('company')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                            name="address" rows="3" autocomplete="street-address">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="province" class="form-label">Provinsi</label>
                            <input type="text" class="form-control @error('province') is-invalid @enderror"
                                id="province" name="province" value="{{ old('province', $user->province) }}">
                            @error('province')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="city" class="form-label">Kota/Kabupaten</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                name="city" value="{{ old('city', $user->city) }}">
                            @error('city')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="district" class="form-label">Kecamatan</label>
                            <input type="text" class="form-control @error('district') is-invalid @enderror"
                                id="district" name="district" value="{{ old('district', $user->district) }}">
                            @error('district')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                id="postal_code" name="postal_code"
                                value="{{ old('postal_code', $user->postal_code) }}" maxlength="10">
                            @error('postal_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card danger-card">
            <div class="card-body p-4">
                <h4 class="section-title text-danger">Hapus Akun</h4>
                <p class="text-muted mb-4">
                    Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen.
                    Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.
                </p>

                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                    <i class="fas fa-trash-alt me-2"></i>Hapus Akun
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="confirmDeleteModalLabel">Konfirmasi Hapus Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan dan semua data Anda akan dihapus secara permanen.</p>

                    <form method="post" action="{{ route('profile.destroy') }}" id="delete-account-form">
                        @csrf
                        @method('delete')

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                id="password" name="password" placeholder="Masukkan password Anda untuk konfirmasi">

                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="delete-account-form" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Re-open modal with errors if validation fails
        @if ($errors->userDeletion->isNotEmpty())
            var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        @endif
    });
</script>
@endpush
