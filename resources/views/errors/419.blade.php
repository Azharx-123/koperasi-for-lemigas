<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesi Berakhir | {{ config('app.name', 'KEP') }}</title>
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
        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block;
            padding: 12px 26px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transition-medium);
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-primary { background: rgb(var(--yellow-primary)); color: rgb(var(--black)); }
        .btn-primary:hover { background: rgb(var(--yellow-dark)); }
        .btn-secondary { background: #fff; color: rgb(var(--black)); border: 1px solid #e2e2e2; }
        .btn-secondary:hover { border-color: rgb(var(--yellow-primary)); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <polyline points="12 7 12 12 15 15"/>
            </svg>
        </div>
        <p class="code">419</p>
        <h1>Sesi Anda Telah Berakhir</h1>
        <p class="desc">Halaman ini sudah terbuka terlalu lama sehingga sesi Anda kedaluwarsa. Muat ulang halaman lalu coba lagi — data yang belum terkirim mungkin perlu diisi ulang.</p>
        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="location.reload()">Muat Ulang Halaman</button>
            <a href="{{ route('welcome') }}" class="btn btn-secondary">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
