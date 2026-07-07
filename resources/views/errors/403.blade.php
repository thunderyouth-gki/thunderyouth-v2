<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - {{ config('app.name', 'ThunderYouth') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 flex items-center justify-center font-sans">
    <div class="max-w-md w-full px-6 py-12 text-center flex flex-col items-center">
        <div class="mb-8 flex justify-center items-center w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full shadow-sm">
            <svg class="w-10 h-10 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold tracking-tight mb-3 text-zinc-900 dark:text-white">
            Akses Ditolak (403)
        </h1>
        <p class="text-zinc-600 dark:text-zinc-400 mb-8 leading-relaxed">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman atau aksi ini.' }}
        </p>

        <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-xl bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-400 px-6 py-3 text-sm font-semibold text-white dark:text-zinc-900 shadow-sm transition">
            Kembali ke Halaman Utama
        </a>
    </div>
</body>
</html>
