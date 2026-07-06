<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">User Access Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage roles and permissions assigned to specific users.</p>
        </div>
        <flux:button wire:click="openNewUserModal" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent transition-all shadow-soft cursor-pointer">New User</flux:button>
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
            <input wire:model.live.debounce.300ms="search" type="text" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search users...">
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
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('email')">
                            Email
                            @if($sortField === 'email') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('name')">
                            Name
                            @if($sortField === 'name') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Roles</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $users->firstItem() + $index }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="{{ $user->is_active ? 'green' : 'red' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        @php
                                            $roleColorClass = match($role->name) {
                                                'Root' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                                'Pengurus' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
                                                'Jemaat' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                                default => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
                                            };
                                        @endphp
                                        <span class="{{ $roleColorClass }} text-xs font-medium px-2 py-0.5 rounded">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic text-xs">No role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->hasRole('Root') && !auth()->user()->hasRole('Root'))
                                    <span class="text-xs text-slate-400 italic">Root (Unmodifiable)</span>
                                @else
                                    <flux:dropdown>
                                        <flux:button size="sm" variant="filled" class="bg-amber-500 hover:bg-amber-600 text-white border-transparent cursor-pointer">Actions <i class="fa-solid fa-chevron-down ml-1 text-xs"></i></flux:button>
                                        <flux:navmenu>
                                            <flux:navmenu.item wire:click="openEditUserModal({{ $user->id }})">Edit Profile</flux:navmenu.item>
                                            @if(!$user->hasRole('Root'))
                                                <flux:navmenu.item wire:click="openManageModal({{ $user->id }})">Manage Access</flux:navmenu.item>
                                                <flux:navmenu.item wire:click="toggleActiveStatus({{ $user->id }})">{{ $user->is_active ? 'Deactivate' : 'Activate' }} User</flux:navmenu.item>
                                                <flux:navmenu.item wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/50">Delete User</flux:navmenu.item>
                                            @endif
                                        </flux:navmenu>
                                    </flux:dropdown>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $users->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <!-- Modal for New User -->
    <flux:modal name="new-user-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Create New User</flux:heading>
            <flux:subheading>Add a new user to the system.</flux:subheading>
        </div>
        
        <form wire:submit.prevent="createUser" class="space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="newName" type="text" required />
                <flux:error name="newName" />
            </flux:field>
            
            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="newEmail" type="email" required />
                <flux:error name="newEmail" />
            </flux:field>
            
            <flux:field>
                <flux:label>Password</flux:label>
                <flux:input wire:model="newPassword" type="password" viewable required />
                <flux:error name="newPassword" />
            </flux:field>
            
            <div class="flex justify-end gap-2 mt-2">
                <flux:modal.close>
                    <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent cursor-pointer">Create User</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal for Edit User -->
    <flux:modal name="edit-user-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Edit User Profile</flux:heading>
            <flux:subheading>Update the user's name, email, or password.</flux:subheading>
        </div>
        
        <form wire:submit.prevent="updateUser" class="space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="editName" type="text" required />
                <flux:error name="editName" />
            </flux:field>
            
            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="editEmail" type="email" required />
                <flux:error name="editEmail" />
            </flux:field>
            
            <flux:field>
                <flux:label>New Password (Optional)</flux:label>
                <flux:input wire:model="editPassword" type="password" viewable placeholder="Leave blank to keep current password" />
                <flux:error name="editPassword" />
            </flux:field>
            
            <div class="flex justify-end gap-2 mt-2">
                <flux:modal.close>
                    <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent cursor-pointer">Save Changes</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal for Manage Access -->
    <flux:modal name="manage-user-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Manage Access</flux:heading>
            <flux:subheading>Assign roles and permissions for this user.</flux:subheading>
        </div>
        
        <form wire:submit.prevent="saveAccess" class="space-y-6">
            <div class="space-y-3">
                <flux:select wire:model="selectedRole" label="Roles" placeholder="Select a role...">
                    @forelse($allRoles as $role)
                        <flux:select.option value="{{ $role->name }}">{{ $role->name }}</flux:select.option>
                    @empty
                        <flux:select.option value="" disabled>No roles available.</flux:select.option>
                    @endforelse
                </flux:select>
            </div>

            <div class="space-y-3">
                <flux:heading size="sm">Direct Permissions</flux:heading>
                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                    @forelse($allPermissions as $permission)
                        <flux:checkbox wire:model="selectedPermissions" value="{{ $permission->name }}" label="{{ $permission->name }}" />
                    @empty
                        <span class="text-sm text-slate-500">No permissions available.</span>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <flux:modal.close>
                    <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent cursor-pointer">Save Changes</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal for Delete Confirmation -->
    <flux:modal name="delete-user-modal" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Delete User</flux:heading>
            <flux:subheading>Are you sure you want to permanently delete this user? This action cannot be undone and will delete all associated data.</flux:subheading>
        </div>
        
        <div class="flex justify-end gap-2 mt-4">
            <flux:modal.close>
                <flux:button class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="deleteUser" class="bg-red-600 hover:bg-red-700 text-white border-transparent cursor-pointer">Yes, Delete</flux:button>
        </div>
    </flux:modal>
</div>
