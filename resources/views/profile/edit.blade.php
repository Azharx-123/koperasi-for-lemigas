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
            --danger: 220, 53, 69;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(var(--gray-100), 0.95), rgba(255, 255, 255, 0.95));
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(var(--yellow-primary), 0.1);
            z-index: -1;
            animation: float 15s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -30%;
            left: -30%;
            width: 80%;
            height: 80%;
            border-radius: 50%;
            background: rgba(var(--yellow-dark), 0.05);
            z-index: -1;
            animation: float 20s ease-in-out infinite reverse;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(5%, 5%) rotate(5deg);
            }

            100% {
                transform: translate(0, 0) rotate(0deg);
            }
        }

        .profile-container {
            max-width: 800px;
            margin: auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            overflow: hidden;
            position: relative;
            margin-bottom: 2rem;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg,
                    rgb(var(--yellow-primary)),
                    rgb(var(--yellow-dark)),
                    rgb(var(--yellow-light)));
        }

        .danger-card::before {
            background: linear-gradient(90deg,
                    rgb(var(--danger)),
                    rgb(var(--danger)),
                    rgba(var(--danger), 0.7));
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .profile-header h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: rgb(var(--gray-800));
            margin-bottom: 0.5rem;
        }

        .profile-header p {
            color: rgb(var(--gray-600));
        }

        .form-control {
            border: 2px solid rgb(var(--gray-200));
            border-radius: 10px;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(var(--yellow-primary), 0.1);
            border-color: rgb(var(--yellow-primary));
        }

        .form-label {
            font-weight: 500;
            color: rgb(var(--gray-800));
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: rgb(var(--yellow-primary));
            border: none;
            color: rgb(var(--gray-800));
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background: rgb(var(--yellow-dark));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(var(--yellow-primary), 0.3);
        }

        .btn-danger {
            background: rgb(var(--danger));
            border: none;
            color: white;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: rgba(var(--danger), 0.85);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(var(--danger), 0.3);
        }

        .btn-secondary {
            background: rgb(var(--gray-200));
            border: none;
            color: rgb(var(--gray-800));
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgb(var(--gray-300));
            transform: translateY(-2px);
        }

        .section-title {
            font-weight: 600;
            color: rgb(var(--gray-800));
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(var(--yellow-primary), 0.3);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem;
        }

        .alert-success {
            background: rgba(var(--yellow-light), 0.2);
            color: rgb(var(--yellow-dark));
        }

        .invalid-feedback {
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .avatar-section {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgb(var(--yellow-primary));
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .avatar-info {
            margin-left: 1.5rem;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 2px solid rgba(var(--danger), 0.2);
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1.5rem;
        }

        @media (max-width: 576px) {
            .profile-container {
                padding: 1rem;
            }

            .card {
                border-radius: 15px;
            }

            .avatar {
                width: 80px;
                height: 80px;
            }
        }
    </style>
@endpush

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