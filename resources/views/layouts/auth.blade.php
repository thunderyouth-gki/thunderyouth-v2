<x-layouts.app :title="$title ?? null">
    <div class="view-section fade-in pt-10 pb-20">
        <div class="max-w-md mx-auto px-4 sm:px-6">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
