<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Members Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage church members and their personal information.</p>
        </div>
        <flux:button wire:click="openNewMemberModal" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent transition-all shadow-soft cursor-pointer">New Member</flux:button>
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
            <input wire:model.live.debounce.300ms="search" type="text" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search members...">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('member_number')">
                            Member ID
                            @if($sortField === 'member_number') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('name')">
                            Name
                            @if($sortField === 'name') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('email')">
                            Email / Phone
                            @if($sortField === 'email') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">User Account</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $member->member_number ?? '-' }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                {{ $member->name }}
                                <div class="text-xs text-slate-500 font-normal">{{ $member->gender === 'L' ? 'Male' : 'Female' }} | {{ $member->blood_type }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if(empty($member->email) && empty($member->phone_number))
                                    <div>-</div>
                                @else
                                    @if(!empty($member->email))
                                        <div>{{ $member->email }}</div>
                                    @endif
                                    @if(!empty($member->phone_number))
                                        <div class="text-xs text-slate-500">{{ $member->phone_number }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($member->user)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <i class="fa-solid fa-link"></i> {{ $member->user->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-xs">Not linked</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($member->status) {
                                        'Active' => 'green',
                                        'Abroad' => 'amber',
                                        'Deceased' => 'zinc',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $statusColor }}">{{ $member->status }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:dropdown>
                                    <flux:button size="sm" variant="filled" class="bg-amber-500 hover:bg-amber-600 text-white border-transparent cursor-pointer">Actions <i class="fa-solid fa-chevron-down ml-1 text-xs"></i></flux:button>
                                    <flux:navmenu>
                                        <flux:navmenu.item wire:click="openEditMemberModal({{ $member->id }})">Edit Details</flux:navmenu.item>
                                        <flux:navmenu.item wire:click="confirmDelete({{ $member->id }})" class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/50">Delete Member</flux:navmenu.item>
                                    </flux:navmenu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $members->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <!-- Modal for Member -->
    <flux:modal name="manage-member-modal" class="md:w-[40rem] p-0">
        <div class="flex flex-col max-h-[85vh]">
            <!-- Sticky Header -->
            <div class="shrink-0 p-6 border-b border-slate-200 dark:border-slate-700">
                <flux:heading size="lg">{{ $manageMemberId ? 'Edit Member' : 'New Member' }}</flux:heading>
                <flux:subheading>Fill in the personal data of the member.</flux:subheading>
            </div>
            
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="member-form" wire:submit.prevent="saveMember" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Full Name</flux:label>
                            <flux:input wire:model="name" type="text" placeholder="e.g. John Doe" required />
                            <flux:error name="name" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Member ID (Nomor Anggota)</flux:label>
                            <flux:input wire:model="member_number" type="text" placeholder="e.g. 001/TY/2026" />
                            <flux:error name="member_number" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Place of Birth</flux:label>
                            <flux:input wire:model="place_of_birth" type="text" placeholder="e.g. Jakarta" />
                            <flux:error name="place_of_birth" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Date of Birth</flux:label>
                            <flux:input wire:model="date_of_birth" type="date" placeholder="mm/dd/yyyy" />
                            <flux:error name="date_of_birth" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Address</flux:label>
                            <flux:textarea wire:model="address" rows="3" placeholder="e.g. Jl. Sudirman No. 1" />
                            <flux:error name="address" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Email Address</flux:label>
                            <div class="relative">
                                <flux:input wire:model="email" type="email" placeholder="e.g. john@example.com" :readonly="!empty($user_id)" :class="!empty($user_id) ? 'bg-slate-50 dark:bg-slate-800 text-slate-500' : ''" />
                                @if(!empty($user_id))
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none" title="Email is synced with User Account">
                                        <i class="fa-solid fa-lock text-slate-400 text-xs"></i>
                                    </div>
                                @endif
                            </div>
                            <flux:error name="email" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Phone Number</flux:label>
                            <flux:input wire:model="phone_number" type="tel" placeholder="e.g. 081234567890" />
                            <flux:error name="phone_number" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Gender</flux:label>
                            <flux:select wire:model="gender" placeholder="-- Choose Gender --">
                                <flux:select.option value="L">Laki-laki (L)</flux:select.option>
                                <flux:select.option value="P">Perempuan (P)</flux:select.option>
                            </flux:select>
                            <flux:error name="gender" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Blood Type</flux:label>
                            <flux:select wire:model="blood_type" placeholder="-- Choose Blood Type --">
                                <flux:select.option value="A">A</flux:select.option>
                                <flux:select.option value="B">B</flux:select.option>
                                <flux:select.option value="AB">AB</flux:select.option>
                                <flux:select.option value="O">O</flux:select.option>
                            </flux:select>
                            <flux:error name="blood_type" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="status" placeholder="-- Choose Status --" required>
                                <flux:select.option value="Active">Active</flux:select.option>
                                <flux:select.option value="Abroad">Abroad</flux:select.option>
                                <flux:select.option value="Deceased">Deceased</flux:select.option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Linked User Account</flux:label>
                            <div class="relative">
                                <flux:input wire:model.live.debounce.300ms="userSearch" type="text" placeholder="Type to search for a user..." autocomplete="off" />
                                
                                @if(strlen($userSearch) > 0 && empty($user_id))
                                    <div class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        @forelse($users as $user)
                                            <div wire:click="selectUser({{ $user->id }}, '{{ addslashes($user->name) }} ({{ addslashes($user->email) }})')" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer text-sm flex justify-between items-center transition-colors">
                                                <div>
                                                    <div class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</div>
                                                    <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="px-4 py-3 text-sm text-slate-500 text-center">No users found.</div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                            @if($user_id)
                                <div class="mt-2 text-sm flex items-center gap-2">
                                    <span class="text-green-600 dark:text-green-400 font-medium"><i class="fa-solid fa-check-circle"></i> Selected</span>
                                    <button type="button" wire:click="selectUser(null, '')" class="text-red-500 hover:text-red-700 text-xs underline transition-colors">Clear Selection</button>
                                </div>
                            @endif
                            <input type="hidden" wire:model="user_id" />
                            <flux:error name="user_id" />
                        </flux:field>
                    </div>
                </form>
            </div>
            
            <!-- Sticky Footer -->
            <div class="shrink-0 p-6 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2 bg-slate-50 dark:bg-slate-800/50">
                <flux:modal.close>
                    <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" form="member-form" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent cursor-pointer">Save Member</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal for Delete Confirmation -->
    <flux:modal name="delete-member-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Delete Member</flux:heading>
            <flux:subheading>Are you sure you want to permanently delete this member? This action cannot be undone.</flux:subheading>
        </div>
        
        <div class="flex justify-end gap-2 mt-4">
            <flux:modal.close>
                <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="deleteMember" class="bg-red-600 hover:bg-red-700 text-white border-transparent cursor-pointer">Yes, Delete</flux:button>
        </div>
    </flux:modal>
</div>
