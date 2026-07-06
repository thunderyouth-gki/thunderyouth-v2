<nav class="fixed top-0 w-full bg-surface/95 backdrop-blur-md z-40 border-b border-accent shadow-sm" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center cursor-pointer">
                <a href="{{ route('home') }}" class="font-heading font-extrabold text-2xl tracking-tighter text-primary flex items-center gap-2">
                    <img src="{{ asset('storage/thunder-logo.png') }}" alt="Thunder Youth Logo" class="w-8 h-8 object-contain">
                    <span>THUNDER<span class="text-secondary italic">YOUTH!</span></span>
                </a>
            </div>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'text-primary font-semibold' : 'text-textlight font-medium' }} hover:text-primary transition">Home</a>
                <a href="{{ route('services') }}" class="nav-link {{ request()->routeIs('services') ? 'text-primary font-semibold' : 'text-textlight font-medium' }} hover:text-primary transition">Services</a>
                <a href="{{ route('events') }}" class="nav-link {{ request()->routeIs('events') ? 'text-primary font-semibold' : 'text-textlight font-medium' }} hover:text-primary transition">Events</a>
                <a href="{{ route('prayer') }}" class="nav-link {{ request()->routeIs('prayer') ? 'text-primary font-semibold' : 'text-textlight font-medium' }} hover:text-primary transition">Prayer Tree</a>
                
                @hasanyrole('Root|Pengurus')
                    <!-- Admin Tab -->
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin*') ? 'text-yellow-600 dark:text-yellow-400 font-bold' : 'text-yellow-600 dark:text-yellow-500 font-semibold hover:text-yellow-700 dark:hover:text-yellow-400' }} transition">
                        <i class="fa-solid fa-gauge mr-1"></i> Admin Panel
                    </a>
                @endhasanyrole
                
                <!-- Login Button / User Profile -->
                <div class="flex items-center gap-4 border-l border-accent pl-6">
                    @guest
                        <button x-data @click="$dispatch('open-auth-modal', { view: 'login' })" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-full font-medium transition shadow-soft flex items-center gap-2">
                            <i class="fa-solid fa-user-circle"></i> Sign In
                        </button>
                    @endguest

                    @auth
                        <div class="flex items-center gap-3 cursor-pointer group" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
                            <div class="text-right hidden md:block" @click="userMenuOpen = !userMenuOpen">
                                <div class="text-sm font-bold text-text">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] text-textlight uppercase">{{ auth()->user()->roles->first()?->name ?? 'Jemaat' }}</div>
                            </div>
                            <div class="relative">
                                <div @click="userMenuOpen = !userMenuOpen" class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold shadow-soft hover:opacity-90 transition">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                
                                <div x-show="userMenuOpen" x-cloak class="absolute top-12 right-0 bg-surface rounded-xl shadow-card border border-accent w-48 py-2 text-left z-50">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-text hover:text-primary hover:bg-primary/10 font-medium transition">
                                        <i class="fa-regular fa-user mr-2 text-textlight"></i> Profil
                                    </a>
                                    <div class="h-px bg-accent my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-500/10 font-semibold transition">
                                            <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center gap-3">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-primary hover:text-secondary focus:outline-none p-2">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-surface border-t border-accent absolute w-full left-0 shadow-lg z-50">
        <div class="px-4 pt-2 pb-6 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-3 rounded-md text-base {{ request()->routeIs('home') ? 'font-semibold text-primary bg-primary/5' : 'font-medium text-textlight hover:text-primary hover:bg-primary/5' }}">Home</a>
            <a href="{{ route('services') }}" class="block px-3 py-3 rounded-md text-base {{ request()->routeIs('services') ? 'font-semibold text-primary bg-primary/5' : 'font-medium text-textlight hover:text-primary hover:bg-primary/5' }}">Services</a>
            <a href="{{ route('events') }}" class="block px-3 py-3 rounded-md text-base {{ request()->routeIs('events') ? 'font-semibold text-primary bg-primary/5' : 'font-medium text-textlight hover:text-primary hover:bg-primary/5' }}">Events</a>
            <a href="{{ route('prayer') }}" class="block px-3 py-3 rounded-md text-base {{ request()->routeIs('prayer') ? 'font-semibold text-primary bg-primary/5' : 'font-medium text-textlight hover:text-primary hover:bg-primary/5' }}">Prayer Tree</a>
            
            @hasanyrole('Root|Pengurus')
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-3 rounded-md text-base font-bold {{ request()->is('admin*') ? 'text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-500/10' : 'text-yellow-600 dark:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-500/10' }} transition"><i class="fa-solid fa-gauge mr-1"></i> Admin Panel</a>
            @endhasanyrole
            
            <div class="pt-4 pb-2 border-t border-accent mt-2">
                @guest
                    <button x-data @click="$dispatch('open-auth-modal', { view: 'login' }); mobileMenuOpen = false" class="w-full bg-primary text-white px-4 py-3 rounded-xl font-medium shadow-soft">
                        <i class="fa-solid fa-user-circle mr-2"></i> Sign In
                    </button>
                @endguest
                @auth
                    <a href="{{ route('profile.edit') }}" class="block w-full text-left bg-accent/30 text-text px-4 py-3 rounded-xl font-medium mb-2 border border-accent hover:border-primary transition">
                        <i class="fa-regular fa-user mr-2 text-textlight"></i> Profil Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left bg-red-500/10 text-red-600 px-4 py-3 rounded-xl font-bold shadow-sm hover:bg-red-500/20 transition">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Log Out
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
