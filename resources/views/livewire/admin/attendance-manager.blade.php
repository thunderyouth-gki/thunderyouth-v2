<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Attendance Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage church attendance records manually.</p>
        </div>
        <flux:button wire:click="openNewAttendanceModal" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent transition-all shadow-soft cursor-pointer">Record Attendance</flux:button>
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
            <input wire:model.live.debounce.300ms="search" type="text" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search by name or service...">
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
                        <th scope="col" class="px-6 py-4 font-semibold">Service</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Name (Member / Guest)</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Method</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $attendance->check_in_time->format('d M Y') }}
                                <div class="text-xs text-slate-500 font-normal">{{ $attendance->check_in_time->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                {{ $attendance->service->theme ?: str($attendance->service->service_type)->replace('_', ' ')->title() }}
                            </td>
                            <td class="px-6 py-4">
                                @if($attendance->member)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        <i class="fa-solid fa-id-card"></i> {{ $attendance->member->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200">
                                        <i class="fa-solid fa-user-tag"></i> {{ $attendance->guest_name }} (Guest)
                                    </span>
                                @endif
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
                            <td class="px-6 py-4 text-right">
                                <flux:dropdown>
                                    <flux:button size="sm" variant="filled" class="bg-amber-500 hover:bg-amber-600 text-white border-transparent cursor-pointer">Actions <i class="fa-solid fa-chevron-down ml-1 text-xs"></i></flux:button>
                                    <flux:navmenu>
                                        <flux:navmenu.item wire:click="openEditAttendanceModal({{ $attendance->id }})">Edit Record</flux:navmenu.item>
                                        <flux:navmenu.item wire:click="confirmDelete({{ $attendance->id }})" class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/50">Delete Record</flux:navmenu.item>
                                    </flux:navmenu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No attendance records found.</td>
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

    <!-- Modal for Attendance -->
    <flux:modal name="manage-attendance-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">{{ $manageAttendanceId ? 'Edit Attendance' : 'Record Attendance' }}</flux:heading>
            <flux:subheading>Manually add or edit an attendance record.</flux:subheading>
        </div>
        
        <form wire:submit.prevent="saveAttendance" class="space-y-6">
            <flux:field>
                <flux:label>Service Date</flux:label>
                <flux:input wire:model.live="service_date" type="date" required />
                <flux:error name="service_date" />
                @if($service_date)
                    @if($services->isNotEmpty())
                        <div class="mt-2">
                            <div class="flex items-center gap-1.5 text-sm text-green-600 dark:text-green-400 font-medium">
                                <i class="fa-solid fa-check-circle"></i> Service found!
                            </div>
                            <div class="mt-0.5 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                {{ $services->first()->theme ?: str($services->first()->service_type)->replace('_', ' ')->title() }}
                            </div>
                        </div>
                    @else
                        @if(!$errors->has('service_date'))
                            <div class="mt-3 text-sm font-medium text-red-500 dark:text-red-400 flex items-center gap-1.5">
                                <flux:icon name="exclamation-triangle" variant="mini" />
                                <span>No service found for this date.</span>
                            </div>
                        @endif
                    @endif
                @endif
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model.live="is_guest" label="This attendance is for a guest" />
            </flux:field>

            @if(!$is_guest)
                <flux:field>
                    <flux:label>Registered Member</flux:label>
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="memberSearch" type="text" placeholder="Type to search for a member..." autocomplete="off" />
                        
                        @if(strlen($memberSearch) > 0 && empty($member_id))
                            <div class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                @forelse($members as $member)
                                    <div wire:click="selectMember({{ $member->id }}, '{{ addslashes($member->name) }} ({{ addslashes($member->member_number) }})')" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer text-sm flex justify-between items-center transition-colors">
                                        <div>
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $member->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $member->member_number }} | {{ $member->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-3 text-sm text-slate-500 text-center">No members found.</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                    @if($member_id)
                        <div class="mt-2 text-sm flex items-center gap-2">
                            <span class="text-green-600 dark:text-green-400 font-medium"><i class="fa-solid fa-check-circle"></i> Selected</span>
                            <button type="button" wire:click="selectMember(null, '')" class="text-red-500 hover:text-red-700 text-xs underline transition-colors">Clear Selection</button>
                        </div>
                    @endif
                    <input type="hidden" wire:model="member_id" />
                    <flux:error name="member_id" />
                </flux:field>
            @else
                <flux:field>
                    <flux:label>Guest Name</flux:label>
                    <flux:input wire:model="guest_name" type="text" placeholder="e.g. John Doe" required />
                    <flux:error name="guest_name" />
                </flux:field>
            @endif

            <flux:field>
                <flux:label>Check In Time</flux:label>
                <flux:input wire:model="check_in_time" type="time" required />
                <flux:error name="check_in_time" />
            </flux:field>

            <div class="flex justify-end gap-2 mt-4">
                <flux:modal.close>
                    <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent cursor-pointer">Save Record</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal for Delete Confirmation -->
    <flux:modal name="delete-attendance-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Delete Record</flux:heading>
            <flux:subheading>Are you sure you want to permanently delete this attendance record? This action cannot be undone.</flux:subheading>
        </div>
        
        <div class="flex justify-end gap-2 mt-4">
            <flux:modal.close>
                <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="deleteAttendance" class="bg-red-600 hover:bg-red-700 text-white border-transparent cursor-pointer">Yes, Delete</flux:button>
        </div>
    </flux:modal>
</div>
