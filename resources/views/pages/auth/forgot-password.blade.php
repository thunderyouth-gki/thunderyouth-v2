<x-layouts.auth :title="__('Forgot password')">
    <div class="bg-surface rounded-3xl p-8 shadow-card border border-accent">
        <div class="mb-6 text-center">
            <div class="w-14 h-14 bg-brandlight rounded-2xl flex items-center justify-center mx-auto mb-3 text-primary text-xl">
                <i class="fa-solid fa-key"></i>
            </div>
            <h2 class="text-2xl font-heading font-extrabold text-primary">{{ __('Forgot password') }}</h2>
            <p class="text-xs text-textlight mt-1 max-w-sm mx-auto leading-relaxed">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-center text-sm font-bold text-emerald-600 bg-emerald-50 py-2 rounded-lg" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Email address') }}</label>
                <input name="email" value="{{ old('email') }}" type="email" required autofocus placeholder="email@example.com" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('email') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-xl font-bold transition shadow-soft text-sm flex justify-center items-center gap-2">
                    <i class="fa-regular fa-paper-plane text-xs"></i> {{ __('Email password reset link') }}
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-xs text-textlight">
            <a href="{{ url('/?login=1') }}" class="text-primary font-bold hover:underline">
                <i class="fa-solid fa-arrow-left text-[10px] mr-1"></i> {{ __('Back to log in') }}
            </a>
        </div>
    </div>
</x-layouts.auth>
