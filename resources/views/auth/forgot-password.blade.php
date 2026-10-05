@extends('layouts.app')

@push('styles')
    @vite('resources/css/pages/auth-forgot-password.css')
@endpush

@section('title', 'Lupa Password')

@section('content')
    <div class="container forgot-password-container my-5">
        <div class="logo-container">
            <img src="{{ \App\Helpers\ImageHelper::url($company->logo) }}" alt="Logo {{ $company->name }}">
            <h4>Reset Password</h4>
            <p class="text-muted">Recover your account</p>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <div class="description">
                    {{ __('No problem. Just enter your email address and we will send you a password reset link to choose a new one.') }}
                </div>

                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0">
                            <a href="{{ route('login') }}" class="text-decoration-none fw-medium">
                                <i class="fas fa-arrow-left me-1"></i> Back to Login
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
