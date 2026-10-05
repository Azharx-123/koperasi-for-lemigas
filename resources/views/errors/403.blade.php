<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | {{ config('app.name', 'KEP') }}</title>
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
            --gray-600: 108, 117, 125;
            --black: 33, 37, 41;
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
        .wrap { max-width: 540px; text-align: center; }
        .icon {
            width: 72px;
            height: 72px;
            border-radius: var(--radius-full);
            background: rgba(var(--yellow-primary), 0.15);
            color: rgb(var(--yellow-dark));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: var(--spacing-md);
        }
        .code {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(72px, 16vw, 128px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
            color: rgb(var(--black));
            margin: 0 0 var(--spacing-sm);
        }
        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 22px;
            margin: 0 0 12px;
        }
        p.desc {
            color: rgb(var(--gray-600));
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 var(--spacing-lg);
        }
        .btn {
            display: inline-block;
            padding: 12px 26px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transition-medium);
        }
        .btn-primary { background: rgb(var(--yellow-primary)); color: rgb(var(--black)); }
        .btn-primary:hover { background: rgb(var(--yellow-dark)); }
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
        <p class="code">403</p>
        <h1>Akses Ditolak</h1>
        <p class="desc">Anda tidak memiliki izin untuk mengakses halaman ini. Jika menurut Anda ini keliru, hubungi admin koperasi.</p>
        <a href="{{ route('welcome') }}" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</body>
</html>
