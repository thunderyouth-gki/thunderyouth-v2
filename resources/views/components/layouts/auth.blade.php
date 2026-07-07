<x-layouts.app :title="$title ?? null">
    <div class="view-section fade-in pt-10 pb-20">
        <div class="max-w-lg mx-auto px-4 sm:px-6 w-full">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
