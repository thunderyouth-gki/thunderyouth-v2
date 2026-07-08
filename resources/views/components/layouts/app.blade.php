<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Thunder Youth - GKI Guntur' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/thunder-logo.png') }}">
    <!-- Tailwind CSS (Using Vite plugin) -->
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Plus Jakarta Sans & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/passkeys.js'])
    
    <script>
        // Initialize dark mode before page render to prevent flicker
        function applyTheme() {
            // Read from our app-specific storage key to avoid localhost conflicts
            const appearance = localStorage.getItem('ty_appearance') || 'light';
            
            if (appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in-right {
            animation: slideInRight 0.4s ease-out forwards;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        .timer-bar {
            animation: timerBar 3.5s linear forwards;
        }
        @keyframes timerBar {
            from { width: 100%; }
            to { width: 0%; }
        }
        /* Custom floating animation for SVG elements */
        .float-animation {
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .wave-animation {
            animation: wave 6s linear infinite;
        }
        @keyframes wave {
            0% { transform: translateX(0); }
            50% { transform: translateX(-15px); }
            100% { transform: translateX(0); }
        }
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body class="bg-background text-text font-sans antialiased overflow-x-hidden flex flex-col min-h-screen">

    <!-- Navigation Bar Component -->
    <x-navigation />

    <!-- Main Content Container -->
    <main class="pt-20 flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-surface border-t border-accent py-12 mt-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="font-heading font-extrabold text-2xl tracking-tighter text-primary flex items-center gap-2">
                        <img src="{{ asset('storage/thunder-logo.png') }}" alt="Thunder Youth Logo" class="w-8 h-8 object-contain">
                        <span>THUNDER<span class="text-secondary italic">YOUTH!</span></span>
                    </a>
                </div>
                <div class="text-textlight text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} Thunder Youth GKI Guntur. All rights reserved.
                </div>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-brandlight text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-brandlight text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-brandlight text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-colors duration-300">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Auth Modal Blade Component -->
    <x-auth-modal />

    <!-- Demo Controller Sandbox -->
    <x-demo-controller />

    <!-- Global Toast Notification -->
    <x-toast-notification />

    @livewireScripts
    @fluxScripts
    <script>
        // Alpine is recommended for these interactions. We will use it if needed, or stick to Vanilla JS for simple things.
    </script>
</body>
</html>
