<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-primary">My Dashboard</h1>
            <p class="text-textlight mt-2">Welcome back, {{ auth()->user()->member?->name ?? auth()->user()->name }}!</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Attendance History -->
            <div class="bg-surface rounded-2xl shadow-card border border-accent p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-text flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                        Recent Attendance
                    </h2>
                </div>
                
                @if($attendances->isEmpty())
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-accent/30 text-textlight mb-4">
                            <i class="fa-solid fa-ghost text-2xl"></i>
                        </div>
                        <p class="text-textlight font-medium">No recent attendance found.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($attendances as $attendance)
                            <div class="flex items-start gap-4 p-4 rounded-xl border border-accent hover:border-primary/30 hover:bg-primary/5 transition-all">
                                <div class="w-12 h-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                                    <div class="text-center">
                                        <div class="text-xs font-bold uppercase">{{ $attendance->event->event_date->format('M') }}</div>
                                        <div class="text-lg font-extrabold leading-none">{{ $attendance->event->event_date->format('d') }}</div>
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <h3 class="font-bold text-text line-clamp-1">
                                        {{ $attendance->event->theme ?: ($attendance->event->eventType->name == 'Other (Lainnya)' ? $attendance->event->custom_event_type : str($attendance->event->service_type)->replace('_', ' ')->title()) }}
                                    </h3>
                                    <p class="text-sm text-textlight mt-1">
                                        {{ $attendance->event->eventType->name }} • {{ $attendance->method }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Upcoming Events -->
            <div class="bg-surface rounded-2xl shadow-card border border-accent p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-text flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-secondary"></i>
                        Upcoming Events
                    </h2>
                </div>

                @if($upcomingEvents->isEmpty())
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-accent/30 text-textlight mb-4">
                            <i class="fa-solid fa-mug-hot text-2xl"></i>
                        </div>
                        <p class="text-textlight font-medium">No upcoming events right now.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($upcomingEvents as $event)
                            <div class="p-4 rounded-xl border border-accent flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                                <div class="w-full sm:w-16 h-16 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center flex-shrink-0">
                                    <div class="text-center">
                                        <div class="text-xs font-bold uppercase">{{ $event->event_date->format('M') }}</div>
                                        <div class="text-xl font-extrabold leading-none">{{ $event->event_date->format('d') }}</div>
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <h3 class="font-bold text-text">{{ $event->theme ?: $event->eventType->name }}</h3>
                                    <p class="text-sm text-textlight mt-1">
                                        <i class="fa-regular fa-clock mr-1"></i> {{ $event->parsed_start_time ?? 'TBA' }} - {{ $event->parsed_end_time ?? 'TBA' }}
                                        <span class="mx-2">•</span>
                                        <i class="fa-solid fa-location-dot mr-1"></i> {{ $event->place ?? 'GKI Guntur' }}
                                    </p>
                                </div>
                                <div>
                                    @if(in_array($event->id, $registeredEventIds))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <i class="fa-solid fa-check"></i> Registered
                                        </span>
                                    @else
                                        <flux:button size="sm" wire:click="$dispatch('openModal', 'register-event', {{ $event->id }})" class="bg-primary hover:bg-primary/90 text-white cursor-pointer w-full sm:w-auto">
                                            Register
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
    </div>
    <livewire:user.register-event-modal />
</div>
