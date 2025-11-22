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

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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

        .login-container {
            max-width: 420px;
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

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .logo-container img {
            height: 80px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }

        .logo-container img:hover {
            transform: scale(1.05);
        }

        .logo-container h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: rgb(var(--gray-800));
            margin-bottom: 0.5rem;
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

        .form-check-input:checked {
            background-color: rgb(var(--yellow-primary));
            border-color: rgb(var(--yellow-primary));
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(var(--yellow-primary), 0.1);
            border-color: rgb(var(--yellow-primary));
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

        a {
            color: rgb(var(--yellow-dark));
            transition: color 0.3s ease;
        }

        a:hover {
            color: rgb(var(--yellow-primary));
        }

        footer {
            margin-top: auto;
            background: rgb(var(--black));
            color: white;
            padding: 1.5rem 0;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }

            .card {
                border-radius: 15px;
            }

            .logo-container img {
                height: 60px;
            }

            .logo-container h4 {
                font-size: 1.25rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container login-container my-5">
        <div class="logo-container">
            <img src="{{ Storage::url($company->logo) }}" alt="Logo {{ $company->name }}">
            <h4>Welcome Back!</h4>
            <p class="text-muted">Login to your account</p>
        </div>

        <div class="card">
            <div class="card-body p-4">
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
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

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" required autocomplete="current-password" placeholder="Enter your password">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                        <label class="form-check-label" for="remember_me">Remember me</label>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            Sign In
                        </button>
                    </div>

                    <div class="text-center mb-3">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                Forgot your password?
                            </a>
                        @endif
                    </div>

                    <div class="text-center">
                        <p class="mb-0">Don't have an account?
                            <a href="{{ route('register') }}" class="text-decoration-none fw-medium">
                                Create Account
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
