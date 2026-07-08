<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel - Thunder Youth' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/thunder-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <style>
        .dark {
            color-scheme: dark;
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
<body class="bg-gradient-to-br from-brandlight/50 to-background dark:from-primary/10 dark:to-background text-text font-sans antialiased h-screen overflow-hidden flex dark:[color-scheme:dark]">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-surface border-r border-primary/10 dark:border-primary/20 flex-shrink-0 flex flex-col h-full hidden md:flex transition-all duration-300 relative z-20 shadow-sm">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-primary/10 dark:border-primary/20">
            <a href="{{ route('admin.dashboard') }}" class="font-bold text-xl flex items-center gap-2 text-primary">
                <img src="{{ asset('storage/thunder-logo.png') }}" alt="Thunder Youth Logo" class="w-8 h-8 object-contain">
                Admin Panel
            </a>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-1">
            <div class="text-[10px] font-bold text-textlight uppercase tracking-wider mb-2 px-2">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white font-bold shadow-md shadow-primary/20' : 'text-textlight hover:bg-primary/10 hover:text-primary' }}">
                <i class="fa-solid fa-gauge w-5 text-center"></i> Dashboard
            </a>
            
            <div class="text-[10px] font-bold text-textlight uppercase tracking-wider mt-6 mb-2 px-2">Church Management</div>
            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.services.*') ? 'bg-primary text-white font-bold shadow-md shadow-primary/20' : 'text-textlight hover:bg-primary/10 hover:text-primary' }}">
                <i class="fa-solid fa-calendar-days w-5 text-center"></i> Services
            </a>
            <a href="{{ route('admin.members') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.members') ? 'bg-primary text-white font-bold shadow-md shadow-primary/20' : 'text-textlight hover:bg-primary/10 hover:text-primary' }}">
                <i class="fa-solid fa-address-book w-5 text-center"></i> Members
            </a>
            <a href="{{ route('admin.attendances') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.attendances') ? 'bg-primary text-white font-bold shadow-md shadow-primary/20' : 'text-textlight hover:bg-primary/10 hover:text-primary' }}">
                <i class="fa-solid fa-clipboard-user w-5 text-center"></i> Attendances
            </a>
            
            @canany(['users.view', 'roles.view', 'permissions.view'])
                <div class="text-[10px] font-bold text-textlight uppercase tracking-wider mt-6 mb-2 px-2">Access Control</div>
            @endcanany
            
            @can('users.view')
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.users') ? 'bg-primary text-white font-bold shadow-md shadow-primary/20' : 'text-textlight hover:bg-primary/10 hover:text-primary' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i> User Access
                </a>
            @endcan
            
            @can('roles.view')
                <a href="{{ route('admin.roles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.roles') ? 'bg-primary text-white font-bold shadow-md shadow-primary/20' : 'text-textlight hover:bg-primary/10 hover:text-primary' }}">
                    <i class="fa-solid fa-user-shield w-5 text-center"></i> Roles
                </a>
            @endcan
            
            @can('permissions.view')
                <a href="{{ route('admin.permissions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.permissions') ? 'bg-primary text-white font-bold shadow-md shadow-primary/20' : 'text-textlight hover:bg-primary/10 hover:text-primary' }}">
                    <i class="fa-solid fa-key w-5 text-center"></i> Permissions
                </a>
            @endcan
        </nav>
        
        <!-- Bottom User Info -->
        <div class="p-4 border-t border-primary/10 dark:border-primary/20 bg-surface mt-auto relative" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
            
            <!-- Popup Menu -->
            <div x-show="userMenuOpen" 
                 x-transition:enter="transition ease-out duration-100" 
                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2" 
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                 x-transition:leave="transition ease-in duration-75" 
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2" 
                 class="absolute bottom-full mb-3 left-4 right-4 bg-surface rounded-xl shadow-card border border-primary/10 dark:border-primary/20 overflow-hidden z-50 py-2" x-cloak style="display: none;">
                
                <a href="{{ route('admin.profile') }}" class="block px-4 py-2.5 text-sm font-medium text-text hover:bg-primary/10 hover:text-primary transition">
                    <i class="fa-regular fa-user mr-2 w-4"></i> My Profile
                </a>
                <hr class="border-primary/10 dark:border-primary/20 my-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-bold text-red-600 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2 w-4"></i> Log Out
                    </button>
                </form>
            </div>

            <!-- User Button -->
            <div @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 mb-4 cursor-pointer hover:bg-primary/10 p-2 rounded-xl transition group">
                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs shadow-soft group-hover:scale-105 transition-transform">
                    {{ strtoupper(substr(auth()->user()->member?->name ?? auth()->user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <div class="text-sm font-bold text-text truncate group-hover:text-primary transition-colors">{{ auth()->user()->member?->name ?? auth()->user()->name }}</div>
                    <div class="text-[10px] text-primary font-bold uppercase truncate">{{ auth()->user()->roles->first()?->name ?? 'Jemaat' }}</div>
                </div>
                <i class="fa-solid fa-chevron-up text-textlight text-xs transition-transform duration-200" :class="{'rotate-180': userMenuOpen}"></i>
            </div>
            
            <a href="{{ route('home') }}" class="flex justify-center items-center gap-2 w-full py-2.5 rounded-xl text-xs font-semibold text-primary dark:text-white bg-brandlight dark:bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 hover:text-white transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to Website
            </a>
        </div>
    </aside>
    
    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-primary/10 dark:border-primary/20 flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0 shadow-sm relative z-10">
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
