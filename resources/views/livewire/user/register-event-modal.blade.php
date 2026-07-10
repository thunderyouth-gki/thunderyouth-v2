<div>
    <flux:modal name="register-event" wire:model="show" class="md:w-96">
        @if($event)
        <div class="p-6">
            <h2 class="text-xl font-bold text-text mb-4">Register for Event</h2>
            <div class="mb-6">
                <h3 class="font-bold text-lg text-primary">{{ $event->theme ?: $event->eventType->name }}</h3>
                <p class="text-sm text-textlight mt-2">
                    <i class="fa-regular fa-clock mr-1"></i> {{ $event->parsed_start_time ?? 'TBA' }} - {{ $event->parsed_end_time ?? 'TBA' }}
                </p>
                <p class="text-sm text-textlight mt-1">
                    <i class="fa-solid fa-location-dot mr-1"></i> {{ $event->place ?? 'GKI Guntur' }}
                </p>
            </div>
            <p class="text-text mb-6">Are you sure you want to register for this event? By registering, you confirm your attendance and will be reminded before the event starts.</p>
            <div class="flex justify-end gap-3">
                <flux:button variant="subtle" wire:click="$set('show', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="register">Confirm Registration</flux:button>
            </div>
        </div>
        @endif
    </flux:modal>
</div>
