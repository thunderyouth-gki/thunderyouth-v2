<div class="view-section fade-in pt-10 pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="mb-8">
                <h1 class="text-3xl font-heading font-extrabold text-primary">{{ $heading ?? __('Settings') }}</h1>
                <p class="text-sm text-textlight mt-1">{{ $subheading ?? __('Manage your account settings') }}</p>
            </div>
            
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar -->
                <nav class="w-full md:w-64 flex flex-col gap-2 flex-shrink-0">
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'bg-primary/10 text-primary font-bold' : 'text-textlight hover:bg-accent/50 hover:text-text' }} px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-sm" wire:navigate>
                        <i class="fa-solid fa-user text-xs"></i> {{ __('Profile') }}
                    </a>
                    <a href="{{ route('security.edit') }}" class="{{ request()->routeIs('security.edit') ? 'bg-primary/10 text-primary font-bold' : 'text-textlight hover:bg-accent/50 hover:text-text' }} px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-sm" wire:navigate>
                        <i class="fa-solid fa-lock text-xs"></i> {{ __('Security') }}
                    </a>
                    <a href="{{ route('appearance.edit') }}" class="{{ request()->routeIs('appearance.edit') ? 'bg-primary/10 text-primary font-bold' : 'text-textlight hover:bg-accent/50 hover:text-text' }} px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-sm" wire:navigate>
                        <i class="fa-solid fa-paint-roller text-xs"></i> {{ __('Appearance') }}
                    </a>
                </nav>

                <!-- Content -->
                <div class="flex-1 bg-surface rounded-3xl p-6 md:p-8 shadow-card border border-accent">
                    {{ $slot }}
                </div>
            </div>
        </div>
</div>
