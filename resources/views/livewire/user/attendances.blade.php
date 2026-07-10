<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Attendance History</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">View your past attendance records.</p>
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
            <input wire:model.live.debounce.300ms="search" type="text" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search by event...">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('check_in_time')">
                            Time
                            @if($sortField === 'check_in_time') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">Event</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Method</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $attendance->check_in_time->format('d M Y') }}
                                <div class="text-xs text-slate-500 font-normal">{{ $attendance->check_in_time->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white">
                                    {{ $attendance->event->theme ?: ($attendance->event->eventType->name == 'Other (Lainnya)' ? $attendance->event->custom_event_type : str($attendance->event->service_type)->replace('_', ' ')->title()) }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ $attendance->event->eventType->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $methodColor = match($attendance->method) {
                                        'QR' => 'blue',
                                        'NFC' => 'purple',
                                        'GPS' => 'green',
                                        'Manual' => 'amber',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $methodColor }}">{{ $attendance->method }}</flux:badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $attendances->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>
</div>
