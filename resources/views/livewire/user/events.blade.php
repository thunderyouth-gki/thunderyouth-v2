<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Upcoming Events</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">View and register for upcoming events.</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500 dark:text-slate-400">Show</span>
            <select wire:model.live="perPage" class="border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2 py-1 focus:ring-primary focus:border-primary">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span class="text-sm text-slate-500 dark:text-slate-400">entries</span>
        </div>
        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <i class="fa-solid fa-search text-xs"></i>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search events...">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('event_date')">
                            Date
                            @if($sortField === 'event_date') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">Event</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Location & Time</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($upcomingEvents as $event)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $event->event_date->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $event->theme ?: $event->eventType->name }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ $event->eventType->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                    <i class="fa-regular fa-clock w-4 text-center"></i> 
                                    <span>{{ $event->parsed_start_time ?? 'TBA' }} - {{ $event->parsed_end_time ?? 'TBA' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 mt-1">
                                    <i class="fa-solid fa-location-dot w-4 text-center"></i> 
                                    <span>{{ $event->place ?? 'GKI Guntur' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(in_array($event->id, $registeredEventIds))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        <i class="fa-solid fa-check"></i> Registered
                                    </span>
                                @else
                                    <flux:button size="sm" wire:click="$dispatch('openModal', 'register-event', {{ $event->id }})" class="bg-primary hover:bg-primary/90 text-white cursor-pointer w-full sm:w-auto">
                                        Register
                                    </flux:button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">No upcoming events available for registration.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($upcomingEvents->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $upcomingEvents->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <livewire:user.register-event-modal />
</div>
