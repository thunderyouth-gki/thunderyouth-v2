<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel - Thunder Youth' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/thunder-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
            --color-primary: 74 59 140;
            --color-secondary: 255 184 0;
            --color-background: 248 249 250;
            --color-surface: 255 255 255;
            --color-text: 31 41 55;
            --color-textlight: 107 114 128;
            --color-accent: 229 231 235;
            --color-brandlight: 240 238 253;
        }

        .dark {
            --color-primary: 124 104 217;
            --color-secondary: 255 193 7;
            --color-background: 15 23 42;
            --color-surface: 30 41 59;
            --color-text: 248 250 252;
            --color-textlight: 148 163 184;
            --color-accent: 51 65 85;
            --color-brandlight: 30 27 75;
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
    </style>
    
    @livewireStyles
</head>
<body class="bg-background text-text font-sans antialiased h-screen overflow-hidden flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-surface border-r border-accent flex-shrink-0 flex flex-col h-full hidden md:flex transition-all duration-300 relative z-20 shadow-sm">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-accent">
            <a href="{{ route('admin.dashboard') }}" class="font-bold text-xl flex items-center gap-2 text-primary">
                <img src="{{ asset('storage/thunder-logo.png') }}" alt="Thunder Youth Logo" class="w-8 h-8 object-contain">
                Admin Panel
            </a>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-1">
            <div class="text-[10px] font-bold text-textlight uppercase tracking-wider mb-2 px-2">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary font-bold shadow-sm' : 'text-textlight hover:bg-accent/30 hover:text-text' }}">
                <i class="fa-solid fa-gauge w-5 text-center"></i> Dashboard
            </a>
            
            <div class="text-[10px] font-bold text-textlight uppercase tracking-wider mt-6 mb-2 px-2">Access Control</div>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.users') ? 'bg-primary/10 text-primary font-bold shadow-sm' : 'text-textlight hover:bg-accent/30 hover:text-text' }}">
                <i class="fa-solid fa-users w-5 text-center"></i> User Access
            </a>
            <a href="{{ route('admin.roles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.roles') ? 'bg-primary/10 text-primary font-bold shadow-sm' : 'text-textlight hover:bg-accent/30 hover:text-text' }}">
                <i class="fa-solid fa-user-shield w-5 text-center"></i> Roles
            </a>
            <a href="{{ route('admin.permissions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.permissions') ? 'bg-primary/10 text-primary font-bold shadow-sm' : 'text-textlight hover:bg-accent/30 hover:text-text' }}">
                <i class="fa-solid fa-key w-5 text-center"></i> Permissions
            </a>
        </nav>
        
        <!-- Bottom User Info -->
        <div class="p-4 border-t border-accent bg-surface mt-auto relative" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
            
            <!-- Popup Menu -->
            <div x-show="userMenuOpen" 
                 x-transition:enter="transition ease-out duration-100" 
                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2" 
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                 x-transition:leave="transition ease-in duration-75" 
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2" 
                 class="absolute bottom-full mb-3 left-4 right-4 bg-surface rounded-xl shadow-card border border-accent overflow-hidden z-50 py-2" x-cloak style="display: none;">
                
                <a href="{{ route('admin.profile') }}" class="block px-4 py-2.5 text-sm font-medium text-text hover:bg-accent/30 hover:text-primary transition">
                    <i class="fa-regular fa-user mr-2 w-4"></i> My Profile
                </a>
                <hr class="border-accent my-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-bold text-red-600 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2 w-4"></i> Log Out
                    </button>
                </form>
            </div>

            <!-- User Button -->
            <div @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 mb-4 cursor-pointer hover:bg-accent/20 p-2 rounded-xl transition group">
                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs shadow-soft group-hover:scale-105 transition-transform">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <div class="text-sm font-bold text-text truncate group-hover:text-primary transition-colors">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-primary font-bold uppercase truncate">{{ auth()->user()->roles->first()?->name ?? 'Jemaat' }}</div>
                </div>
                <i class="fa-solid fa-chevron-up text-textlight text-xs transition-transform duration-200" :class="{'rotate-180': userMenuOpen}"></i>
            </div>
            
            <a href="{{ route('home') }}" class="flex justify-center items-center gap-2 w-full py-2.5 rounded-xl text-xs font-semibold text-text bg-accent/30 hover:bg-accent/50 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to Website
            </a>
        </div>
    </aside>
    
    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-surface border-b border-accent flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0 shadow-sm relative z-10">
            <div class="flex items-center">
                <!-- Mobile Brand (hidden on desktop) -->
                <a href="{{ route('admin.dashboard') }}" class="md:hidden font-bold text-lg flex items-center gap-2 text-primary mr-4">
                    <img src="{{ asset('storage/thunder-logo.png') }}" alt="Thunder Youth Logo" class="w-8 h-8 object-contain">
                </a>
                
                <!-- Breadcrumbs -->
                <div class="text-sm font-medium text-textlight hidden sm:flex items-center gap-2">
                    <i class="fa-solid fa-house text-[10px]"></i>
                    <span>/</span>
                    <span class="text-text capitalize">{{ str_replace('admin.', '', request()->route()->getName()) }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-xs text-textlight font-medium hidden sm:block">
                    {{ now()->format('l, j F Y') }}
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    @fluxScripts

    <!-- Global Toast Notification -->
    <x-toast-notification />
</body>
</html>
