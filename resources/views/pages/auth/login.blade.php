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

        @if (session('message'))
            <div class="mb-4 p-4 text-sm font-bold text-blue-800 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 rounded-xl flex items-start gap-3 border border-blue-200 dark:border-blue-800">
                <i class="fa-solid fa-circle-info mt-0.5 text-blue-600 dark:text-blue-400 text-lg"></i>
                <div>
                    {{ session('message') }}
                </div>
            </div>
        @endif

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
        
        <div x-data="{
            loading: false,
            error: null,
            async loginWithPasskey() {
                if (!window.Passkeys) {
                    this.error = 'Passkeys are not loaded yet. Please try again.';
                    return;
                }
                this.loading = true;
                this.error = null;
                try {
                    const response = await window.Passkeys.verify();
                    if (response && response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '{{ route('dashboard') }}';
                    }
                } catch (e) {
                    console.error('Passkey login error:', e);
                    this.error = e.message || 'Could not authenticate with Passkey.';
                } finally {
                    this.loading = false;
                }
            }
        }" class="mt-4">
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-accent"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-surface text-textlight">{{ __('Or continue with') }}</span>
                </div>
            </div>
            
            <button type="button" @click="loginWithPasskey" :disabled="loading" class="w-full bg-surface text-primary border-2 border-brandlight hover:bg-brandlight py-3 rounded-xl font-bold transition shadow-soft text-sm flex justify-center items-center gap-2 disabled:opacity-50">
                <i class="fa-solid fa-fingerprint text-lg"></i> <span x-text="loading ? '{{ __('Authenticating...') }}' : '{{ __('Sign in with Passkey') }}'"></span>
            </button>
            <p x-show="error" x-text="error" class="text-[10px] text-red-500 font-semibold mt-2 text-center" x-cloak></p>
        </div>

        <div class="mt-6 text-center text-xs text-textlight flex flex-col gap-2">
            <div>
                {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}" class="text-primary font-bold hover:underline" wire:navigate>{{ __('Sign up') }}</a>
            </div>
            <div class="pt-2 border-t border-accent mt-2">
                {{ __('Not a member?') }} <a href="{{ route('guest.attendance') }}" class="text-primary font-bold hover:underline" wire:navigate>{{ __('Click Here') }}</a>
            </div>
        </div>
    </div>
</x-layouts.auth>
