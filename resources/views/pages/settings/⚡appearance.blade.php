<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div x-data="{ appearance: localStorage.getItem('ty_appearance') || 'light' }" class="flex flex-col sm:flex-row gap-4">
            <button type="button" @click="appearance = 'light'; localStorage.setItem('ty_appearance', 'light'); document.documentElement.classList.remove('dark')" :class="appearance === 'light' ? 'bg-primary text-white border-primary shadow-soft' : 'bg-surface text-textlight border-accent hover:border-primary'" class="flex-1 py-3 px-4 border rounded-xl font-bold flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-sun"></i> {{ __('Light') }}
            </button>
            <button type="button" @click="appearance = 'dark'; localStorage.setItem('ty_appearance', 'dark'); document.documentElement.classList.add('dark')" :class="appearance === 'dark' ? 'bg-primary text-white border-primary shadow-soft' : 'bg-surface text-textlight border-accent hover:border-primary'" class="flex-1 py-3 px-4 border rounded-xl font-bold flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-moon"></i> {{ __('Dark') }}
            </button>
            <button type="button" @click="appearance = 'system'; localStorage.setItem('ty_appearance', 'system'); if(window.matchMedia('(prefers-color-scheme: dark)').matches) { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') }" :class="appearance === 'system' ? 'bg-primary text-white border-primary shadow-soft' : 'bg-surface text-textlight border-accent hover:border-primary'" class="flex-1 py-3 px-4 border rounded-xl font-bold flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-computer"></i> {{ __('System') }}
            </button>
        </div>
    </x-pages::settings.layout>
</section>
