<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h2 class="text-lg font-bold text-text">{{ __('Delete account') }}</h2>
        <p class="text-sm text-textlight mt-1">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <button type="button" data-test="delete-user-button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-soft text-sm">
            {{ __('Delete account') }}
        </button>
    </flux:modal.trigger>

    <livewire:pages::settings.delete-user-modal />
</section>
