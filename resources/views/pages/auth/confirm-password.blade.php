<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Confirm password')"
            :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('Confirm with passkey')"
            :loading-label="__('Confirming...')"
            :separator="__('Or confirm with password')"
        />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Password') }}</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 flex items-center text-textlight hover:text-text transition focus:outline-none">
                        <i class="fa-solid fa-eye text-sm" x-show="!show"></i>
                        <i class="fa-solid fa-eye-slash text-sm" x-show="show" x-cloak></i>
                    </button>
                </div>
                @error('password') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <button type="submit" data-test="confirm-password-button" class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-xl font-bold transition shadow-soft text-sm">
                {{ __('Confirm') }}
            </button>
        </form>
    </div>
</x-layouts::auth>
