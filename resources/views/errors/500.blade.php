
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kesalahan Server - 500</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 0 1rem;
        }
        .error-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
            max-width: 36rem;
            width: 100%;
            text-align: center;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #4C7660;
            line-height: 1;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        .back-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #4C7660;
            color: white;
            border-radius: 0.375rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .back-button:hover {
            background-color: #3b5a4a;
        }
        .error-icon {
            width: 6rem;
            height: 6rem;
            margin-bottom: 1.5rem;
            color: #4C7660;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <svg xmlns="http://www.w3.org/2000/svg" class="error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 10h-4v4h4v-4z"></path>
                <path d="M22 8a5 5 0 0 0-5-5H7a5 5 0 0 0-5 5v8a5 5 0 0 0 5 5h8a5 5 0 0 0 5-5v-4"></path>
                <line x1="2" y1="2" x2="22" y2="22"></line>
            </svg>
            <div class="error-code">500</div>
            <h1 class="error-title">Kesalahan Server Internal</h1>
            <p class="error-message">Maaf, terjadi kesalahan pada server. Tim teknis kami sudah diberitahukan dan sedang bekerja untuk memperbaikinya.</p>
            <a href="{{ url('/') }}" class="back-button">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
