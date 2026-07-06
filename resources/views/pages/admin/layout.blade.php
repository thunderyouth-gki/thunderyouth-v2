<div class="max-w-4xl w-full">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary mb-6">{{ $heading ?? __('My Profile') }}</h1>
        
        <nav class="flex gap-2 sm:gap-6 border-b border-accent">
            <a href="{{ route('admin.profile') }}" class="px-2 sm:px-4 py-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 {{ request()->routeIs('admin.profile') ? 'border-primary text-primary' : 'border-transparent text-textlight hover:text-text hover:border-accent' }}">
                <i class="fa-solid fa-user"></i> <span class="hidden sm:inline">{{ __('Profile') }}</span>
            </a>
            <a href="{{ route('admin.security') }}" class="px-2 sm:px-4 py-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 {{ request()->routeIs('admin.security') || request()->routeIs('password.confirm') ? 'border-primary text-primary' : 'border-transparent text-textlight hover:text-text hover:border-accent' }}">
                <i class="fa-solid fa-lock"></i> <span class="hidden sm:inline">{{ __('Security') }}</span>
            </a>
            <a href="{{ route('admin.appearance') }}" class="px-2 sm:px-4 py-3 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 {{ request()->routeIs('admin.appearance') ? 'border-primary text-primary' : 'border-transparent text-textlight hover:text-text hover:border-accent' }}">
                <i class="fa-solid fa-paint-roller"></i> <span class="hidden sm:inline">{{ __('Appearance') }}</span>
            </a>
        </nav>
    </div>

    <div class="bg-surface rounded-3xl shadow-card border border-accent p-6 md:p-8">
        {{ $slot }}
    </div>
</div>
