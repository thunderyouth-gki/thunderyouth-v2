@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center mb-4">
    <h1 class="text-2xl font-bold font-heading text-text">{{ $title }}</h1>
    @if ($description)
        <p class="text-sm text-textlight mt-2">{{ $description }}</p>
    @endif
</div>
