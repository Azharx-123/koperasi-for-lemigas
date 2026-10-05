<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dua Faktor | {{ config('app.name', 'KEP') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <style>
        :root {
            --yellow-primary: 255, 215, 0;
            --yellow-dark: 178, 151, 0;
            --gray-100: 248, 249, 250;
            --gray-300: 222, 226, 230;
            --gray-600: 108, 117, 125;
            --black: 33, 37, 41;
            --red: 220, 53, 69;
            --radius-md: 0.5rem;
            --radius-full: 50%;
            --spacing-sm: 1rem;
            --spacing-md: 1.5rem;
            --spacing-lg: 2rem;
            --transition-medium: 0.3s ease;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgb(var(--gray-100));
            font-family: 'Inter', sans-serif;
            color: rgb(var(--black));
            padding: var(--spacing-lg) var(--spacing-md);
        }
        .wrap { max-width: 400px; width: 100%; text-align: center; }
        .icon {
            width: 72px;
            height: 72px;
            border-radius: var(--radius-full);
            background: rgba(var(--yellow-primary), 0.15);
            color: rgb(var(--yellow-dark));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-md);
        }
        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 22px;
            margin: 0 0 12px;
        }
        p.desc {
            color: rgb(var(--gray-600));
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 var(--spacing-lg);
        }
        .card {
            background: #fff;
            border: 1px solid rgb(var(--gray-300));
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            text-align: left;
        }
        label {
            display: block;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
        }
        input[type="text"] {
            width: 100%;
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            letter-spacing: 0.3em;
            text-align: center;
            padding: 12px;
            border: 1px solid rgb(var(--gray-300));
            border-radius: var(--radius-md);
            margin-bottom: 8px;
            transition: var(--transition-medium);
        }
        input[type="text"]:focus {
            outline: none;
            border-color: rgb(var(--yellow-dark));
        }
        .hint {
            font-size: 13px;
            color: rgb(var(--gray-600));
            margin-bottom: var(--spacing-md);
        }
        .error {
            font-size: 13px;
            color: rgb(var(--red));
            margin: -4px 0 var(--spacing-md);
        }
        .btn {
            display: block;
            width: 100%;
            padding: 12px 26px;
            border-radius: var(--radius-md);
            border: none;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition-medium);
            text-align: center;
        }
        .btn-primary { background: rgb(var(--yellow-primary)); color: rgb(var(--black)); }
        .btn-primary:hover { background: rgb(var(--yellow-dark)); }
        .back-link {
            display: inline-block;
            margin-top: var(--spacing-md);
            color: rgb(var(--gray-600));
            font-size: 14px;
            text-decoration: none;
        }
        .back-link:hover { color: rgb(var(--black)); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" aria-hidden="true">
                <rect x="5" y="11" width="14" height="10" rx="2"/>
                <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
            </svg>
        </div>
        <h1>Verifikasi Dua Faktor</h1>
        <p class="desc">Masukkan kode 6 digit dari aplikasi autentikator Anda untuk melanjutkan.</p>

        <div class="card">
            <form method="POST" action="{{ route('two-factor.verify') }}">
                @csrf
                <label for="code">Kode Autentikasi</label>
                <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code"
                    maxlength="17" autofocus placeholder="000000" value="{{ old('code') }}">
                <p class="hint">Kehilangan akses ke aplikasi autentikator? Masukkan salah satu kode pemulihan Anda di kolom yang sama.</p>

                @error('code')
                    <p class="error">{{ $message }}</p>
                @enderror

                <button type="submit" class="btn btn-primary">Verifikasi</button>
            </form>
        </div>

        <a href="/admin/login" class="back-link">&larr; Kembali ke halaman login</a>
    </div>
</body>
</html>
