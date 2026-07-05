<x-layouts.app :title="$title ?? null">
    <div class="view-section fade-in pt-10 pb-20">
        <div class="max-w-md mx-auto px-4 sm:px-6">
            <div class="bg-surface rounded-3xl shadow-soft p-8 border border-accent/60">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.app>
