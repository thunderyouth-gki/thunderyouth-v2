<x-layouts.auth :title="__('Register')">
    <div class="bg-surface rounded-3xl p-8 shadow-card border border-accent">
        <div class="mb-5 text-center">
            <h2 class="text-2xl font-heading font-extrabold text-primary">{{ __('Create an account') }}</h2>
            <p class="text-xs text-textlight mt-1">{{ __('Please enter your details to sign up') }}</p>
        </div>

        @if (session('message'))
            <div class="mb-4 p-4 text-sm font-bold text-blue-800 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 rounded-xl flex items-start gap-3 border border-blue-200 dark:border-blue-800">
                <i class="fa-solid fa-circle-info mt-0.5 text-blue-600 dark:text-blue-400 text-lg"></i>
                <div>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Name') }}</label>
                <input name="name" value="{{ old('name') }}" type="text" required autofocus autocomplete="name" placeholder="Full Name" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('name') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Email address') }}</label>
                <input name="email" value="{{ old('email') }}" type="email" required autocomplete="email" placeholder="email@example.com" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('email') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Password') }}</label>
                <input name="password" type="password" required autocomplete="new-password" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('password') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Confirm Password') }}</label>
                <input name="password_confirmation" type="password" required autocomplete="new-password" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('password_confirmation') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-xl font-bold transition shadow-soft text-sm flex justify-center items-center gap-2">
                    <i class="fa-solid fa-user-plus text-xs"></i> {{ __('Sign up') }}
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-xs text-textlight">
            {{ __('Already have an account?') }} <a href="{{ route('login') }}" class="text-primary font-bold hover:underline" wire:navigate>{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts.auth>
