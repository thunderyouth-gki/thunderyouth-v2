<x-layouts.auth :title="__('Reset password')">
    <div class="bg-surface rounded-3xl p-8 shadow-card border border-accent">
        <div class="mb-5 text-center">
            <h2 class="text-2xl font-heading font-extrabold text-primary">{{ __('Reset password') }}</h2>
            <p class="text-xs text-textlight mt-1">{{ __('Please enter your new password below') }}</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Email address') }}</label>
                <input name="email" value="{{ old('email', request('email')) }}" type="email" required autocomplete="email" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('email') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Password') }}</label>
                <input name="password" type="password" required autofocus autocomplete="new-password" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
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
                    <i class="fa-solid fa-rotate text-xs"></i> {{ __('Reset password') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.auth>
