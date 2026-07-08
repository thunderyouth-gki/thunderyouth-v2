<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name', 'Thunder Youth') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/thunder-logo.png') }}">
    <!-- Tailwind CSS (Using Vite plugin) -->
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Plus Jakarta Sans & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        function applyTheme() {
            const appearance = localStorage.getItem('ty_appearance') || 'light';
            if (appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applyTheme();
    </script>
</head>
<body class="bg-background text-text font-sans antialiased min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <!-- Background Decorators -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-md w-full text-center flex flex-col items-center bg-surface p-8 sm:p-12 rounded-[2rem] shadow-card border border-accent/10">
        
        <div class="mb-8 flex justify-center items-center w-24 h-24 bg-brandlight text-primary rounded-full shadow-soft float-animation">
            @yield('icon')
        </div>
        
        <h1 class="font-heading text-5xl sm:text-6xl font-extrabold tracking-tighter mb-2 text-primary">
            @yield('code')
        </h1>
        
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight mb-4 text-text">
            @yield('title')
        </h2>
        
        <p class="text-textlight mb-8 leading-relaxed text-sm sm:text-base">
            @yield('message')
        </p>

        <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-xl bg-primary hover:bg-primary/90 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition-all duration-300 hover:shadow-glow hover:-translate-y-0.5">
            <i class="fa-solid fa-house mr-2"></i> Back to Homepage
        </a>
    </div>

    <style>
        .float-animation {
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
    </style>
</body>
</html>
