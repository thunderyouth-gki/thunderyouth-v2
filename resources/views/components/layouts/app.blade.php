<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Thunder Youth - GKI Guntur' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/thunder-logo.png') }}">
    <!-- Tailwind CSS (Using CDN for this design mock to preserve Exact config, but ideally should use Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Plus Jakarta Sans & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: 'rgb(var(--color-primary) / <alpha-value>)',
                        secondary: 'rgb(var(--color-secondary) / <alpha-value>)',
                        background: 'rgb(var(--color-background) / <alpha-value>)',
                        surface: 'rgb(var(--color-surface) / <alpha-value>)',
                        text: 'rgb(var(--color-text) / <alpha-value>)',
                        textlight: 'rgb(var(--color-textlight) / <alpha-value>)',
                        accent: 'rgb(var(--color-accent) / <alpha-value>)',
                        brandlight: 'rgb(var(--color-brandlight) / <alpha-value>)'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(74, 59, 140, 0.05)',
                        'card': '0 10px 30px -5px rgba(74, 59, 140, 0.08)',
                        'glow': '0 0 25px rgba(74, 59, 140, 0.25)',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            /* #4A3B8C */ --color-primary: 74 59 140;
            /* #FFB800 */ --color-secondary: 255 184 0;
            /* #F8F9FA */ --color-background: 248 249 250;
            /* #FFFFFF */ --color-surface: 255 255 255;
            /* #1F2937 */ --color-text: 31 41 55;
            /* #6B7280 */ --color-textlight: 107 114 128;
            /* #E5E7EB */ --color-accent: 229 231 235;
            /* #F0EEFD */ --color-brandlight: 240 238 253;
        }

        .dark {
            /* #7c68d9 */ --color-primary: 124 104 217;
            /* #FFC107 */ --color-secondary: 255 193 7;
            /* #0f172a */ --color-background: 15 23 42;
            /* #1e293b */ --color-surface: 30 41 59;
            /* #f8fafc */ --color-text: 248 250 252;
            /* #94a3b8 */ --color-textlight: 148 163 184;
            /* #334155 */ --color-accent: 51 65 85;
            /* #1e1b4b */ --color-brandlight: 30 27 75;
        }

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
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

    @livewireScripts
    @fluxScripts
    <script>
        // Alpine is recommended for these interactions. We will use it if needed, or stick to Vanilla JS for simple things.
        
        // --- GLOBAL TOAST NOTIFICATION ---
        window.showNotification = function(message, type = 'success') {
            const toast = document.createElement('div');
            const icon = type === 'success' ? '<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>' : '<i class="fa-solid fa-circle-exclamation text-red-400 text-sm"></i>';
            toast.className = 'bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-glow fade-in text-xs font-semibold flex items-center gap-2 border border-slate-700';
            toast.innerHTML = `${icon} ${message}`;
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        window.addEventListener('notify', event => {
            showNotification(event.detail.message, event.detail.type || 'success');
        });
    </script>
</body>
</html>
