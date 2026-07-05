<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="space-y-6">
            <!-- Name -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Name') }}</label>
                <input wire:model="name" type="text" required autofocus autocomplete="name" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('name') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Email address') }}</label>
                <input wire:model="email" type="email" required autocomplete="email" class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                @error('email') <span class="mt-1 text-[10px] text-red-500 font-semibold">{{ $message }}</span> @enderror

                @if ($this->hasUnverifiedEmail)
                    <div class="mt-4 text-sm text-amber-700 bg-amber-50 border border-amber-200 p-3 rounded-xl">
                        <p class="mb-2">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ __('Your email address is unverified.') }}
                        </p>
                        <button type="button" class="text-primary font-bold hover:underline" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">
                                <i class="fa-solid fa-check mr-1"></i> {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Save Button -->
            <div class="pt-2 flex items-center justify-end">
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-xl font-bold transition shadow-soft text-sm flex items-center gap-2">
                    <i class="fa-regular fa-floppy-disk text-xs"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <div class="mt-10 pt-10 border-t border-accent">
                <livewire:pages::settings.delete-user-form />
            </div>
        @endif
    </x-pages::settings.layout>
</section>
