<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Permissions Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage individual permissions available in the system.</p>
        </div>
        <flux:button wire:click="openNewPermissionModal" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent transition-all shadow-soft cursor-pointer">New Permission</flux:button>
    </div>

    <!-- Filters and Search -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500 dark:text-slate-400">Show</span>
            <select wire:model.live="perPage" class="border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2 py-1 focus:ring-primary focus:border-primary">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
            <span class="text-sm text-slate-500 dark:text-slate-400">entries</span>
        </div>
        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <i class="fa-solid fa-search text-xs"></i>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search permissions...">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('id')">
                            No
                            @if($sortField === 'id') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold w-full cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('name')">
                            Permission Name
                            @if($sortField === 'name') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($permissions as $index => $permission)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $permissions->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $permission->name }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <flux:button wire:click="editPermission({{ $permission->id }})" size="sm" variant="filled" class="bg-amber-500 hover:bg-amber-600 text-white border-transparent cursor-pointer">Edit</flux:button>
                                    <flux:button wire:click="confirmDelete({{ $permission->id }})" size="sm" variant="filled" class="bg-red-500 hover:bg-red-600 text-white border-transparent cursor-pointer"><i class="fa-solid fa-trash text-xs"></i></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($permissions->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $permissions->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <!-- Modal for Create/Edit Permission -->
    <flux:modal name="permission-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">{{ $permissionId ? 'Edit Permission' : 'Create New Permission' }}</flux:heading>
            <flux:subheading>{{ $permissionId ? 'Update the permission name.' : 'Add a new permission string.' }}</flux:subheading>
        </div>
        
        <form wire:submit.prevent="{{ $permissionId ? 'updatePermission' : 'createPermission' }}" class="space-y-6">
            <flux:field>
                <flux:label>Permission Name</flux:label>
                <flux:input wire:model="name" type="text" required />
                <flux:error name="name" />
            </flux:field>
            
            <div class="flex justify-end gap-2 mt-4">
                <flux:modal.close>
                    <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent cursor-pointer">Save</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal for Delete Confirmation -->
    <flux:modal name="delete-permission-modal" class="md:w-96 space-y-6">
        <div>
            <div class="flex items-center gap-3 text-red-500">
                <flux:icon.exclamation-triangle variant="solid" />
                <flux:heading size="lg">Delete Permission</flux:heading>
            </div>
            <flux:subheading class="mt-2">Are you sure you want to delete this permission? This action cannot be undone.</flux:subheading>
        </div>
        
        <div class="flex justify-end gap-2 mt-4">
            <flux:modal.close>
                <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="deletePermission" class="bg-red-500 hover:bg-red-600 text-white border-transparent cursor-pointer">Yes, Delete</flux:button>
        </div>
    </flux:modal>
</div>
