<x-layouts.auth :title="__('Log in')">
    <div class="bg-surface rounded-3xl p-8 shadow-card border border-accent">
        <div class="mb-6 text-center">
            <div class="w-14 h-14 bg-brandlight rounded-2xl flex items-center justify-center mx-auto mb-3 text-primary text-xl">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </div>
            <h2 class="text-2xl font-heading font-extrabold text-primary">{{ __('Welcome Back') }}</h2>
            <p class="text-xs text-textlight mt-1">{{ __('Log in to your account') }}</p>
        </div>

        <x-auth-session-status class="mb-4 text-center text-sm font-bold text-emerald-600 bg-emerald-50 py-2 rounded-lg" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Email address') }}</label>
                <input name="email" value="{{ old('email') }}" type="email" required autofocus autocomplete="email" placeholder="email@example.com" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('email') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Password') }}</label>
                <input name="password" type="password" required autocomplete="current-password" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('password') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mt-2">
                <label class="flex items-center gap-2 cursor-pointer text-xs text-textlight hover:text-text select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-accent text-primary focus:ring-primary" {{ old('remember') ? 'checked' : '' }}>
                    {{ __('Remember me') }}
                </label>
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-primary hover:underline" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-xl font-bold transition shadow-soft text-sm flex justify-center items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i> {{ __('Log in') }}
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-xs text-textlight">
            {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}" class="text-primary font-bold hover:underline" wire:navigate>{{ __('Sign up') }}</a>
        </div>
    </div>
</x-layouts.auth>
