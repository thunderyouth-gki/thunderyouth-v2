<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserAccessManager extends Component
{
    use \Livewire\WithPagination;

    public string $search = '';
    public string $sortField = 'id';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    // Modal state
    public ?int $manageUserId = null;
    public ?int $deleteUserId = null;
    public string $selectedRole = '';
    /** @var array<int, string> */
    public array $selectedPermissions = [];

    // New User state
    public string $newName = '';
    public string $newEmail = '';
    public string $newPassword = '';

    // Edit User state
    public ?int $editUserId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editPassword = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openManageModal(int $userId): void
    {
        $this->manageUserId = $userId;
        /** @var User $user */
        $user = User::findOrFail($userId);
        $this->manageUserId = (int) $user->id;
        /** @var \Spatie\Permission\Models\Role|null $firstRole */
        $firstRole = $user->roles->first();
        $this->selectedRole = $firstRole ? $firstRole->name : '';
        $this->selectedPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        \Flux::modal('manage-user-modal')->show();
    }

    public function saveAccess(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.edit');

        if ($this->manageUserId == auth()->id()) {
            \Flux::modal('manage-user-modal')->close();
            $this->dispatch('notify', message: 'You cannot change your own access levels.', type: 'error');
            return;
        }

        /** @var User $user */
        $user = User::findOrFail($this->manageUserId);
        
        if ($user->hasRole('Root')) {
            \Flux::modal('manage-user-modal')->close();
            $this->dispatch('notify', message: 'Cannot modify Root user access.');
            return;
        }

        if ($this->selectedRole) {
            if ($this->selectedRole === 'Root') {
                \Flux::modal('manage-user-modal')->close();
                $this->dispatch('notify', message: 'Cannot assign Root role.');
                return;
            }
            $user->syncRoles([$this->selectedRole]);
        } else {
            $user->syncRoles([]);
        }
        
        $user->syncPermissions($this->selectedPermissions);
        
        \Flux::modal('manage-user-modal')->close();
        $this->dispatch('notify', message: 'User access updated successfully!');
    }

    public function openNewUserModal(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.create');

        $this->reset(['newName', 'newEmail', 'newPassword']);
        \Flux::modal('new-user-modal')->show();
    }

    public function createUser(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.create');

        $this->validate([
            'newName' => 'required|string|max:255',
            'newEmail' => 'required|email|unique:users,email',
            'newPassword' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'password' => \Illuminate\Support\Facades\Hash::make($this->newPassword),
        ]);

        // Default new users created here to Pengurus, since admins are creating them
        $user->assignRole('Pengurus');

        $this->reset(['newName', 'newEmail', 'newPassword']);
        \Flux::modal('new-user-modal')->close();
        $this->dispatch('notify', message: 'New user created successfully!');
    }

    public function openEditUserModal(int $userId): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.edit');

        /** @var User $user */
        $user = User::findOrFail($userId);
        
        if ($user->hasRole('Root') && !auth()->user()->hasRole('Root')) {
            $this->dispatch('notify', message: 'Only Root can edit the Root account.');
            return;
        }

        $this->editUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editPassword = ''; // Leave blank to not change
        
        \Flux::modal('edit-user-modal')->show();
    }

    public function updateUser(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.edit');

        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->editUserId,
            'editPassword' => 'nullable|string|min:8',
        ]);

        /** @var User $user */
        $user = User::findOrFail($this->editUserId);
        
        if ($user->hasRole('Root') && !auth()->user()->hasRole('Root')) {
            $this->dispatch('notify', message: 'Only Root can edit the Root account.');
            return;
        }

        $user->name = $this->editName;
        $user->email = $this->editEmail;
        
        if (!empty($this->editPassword)) {
            $user->password = \Illuminate\Support\Facades\Hash::make($this->editPassword);
        }
        
        $user->save();

        $this->reset(['editUserId', 'editName', 'editEmail', 'editPassword']);
        \Flux::modal('edit-user-modal')->close();
        $this->dispatch('notify', message: 'User profile updated successfully!');
    }

    public function toggleActiveStatus(int $userId): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.edit');

        if ($userId == auth()->id()) {
            $this->dispatch('notify', message: 'You cannot deactivate your own account.', type: 'error');
            return;
        }

        /** @var User $user */
        $user = User::findOrFail($userId);
        
        if ($user->hasRole('Root')) {
            $this->dispatch('notify', message: 'Cannot deactivate the Root user.');
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        $this->dispatch('notify', message: "User successfully {$status}.");
    }

    public function confirmDelete(int $userId): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.delete');

        if ($userId == auth()->id()) {
            $this->dispatch('notify', message: 'You cannot delete your own account.', type: 'error');
            return;
        }

        /** @var User $user */
        $user = User::findOrFail($userId);
        
        if ($user->hasRole('Root')) {
            $this->dispatch('notify', message: 'Cannot delete the Root user.');
            return;
        }
        
        $this->deleteUserId = $userId;
        \Flux::modal('delete-user-modal')->show();
    }

    public function deleteUser(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('users.delete');

        if (!$this->deleteUserId) return;

        /** @var User $user */
        $user = User::findOrFail($this->deleteUserId);
        
        if ($user->hasRole('Root')) {
            $this->dispatch('notify', message: 'Cannot delete the Root user.');
            return;
        }

        $user->delete();
        
        $this->deleteUserId = null;
        \Flux::modal('delete-user-modal')->close();
        $this->dispatch('notify', message: 'User deleted successfully.');
    }

    public function render(): \Illuminate\View\View
    {
        $users = User::with('roles', 'permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.user-access-manager', [
            'users' => $users,
            'allRoles' => \Spatie\Permission\Models\Role::where('name', '!=', 'Root')->get(),
            'allPermissions' => \Spatie\Permission\Models\Permission::all(),
        ])->layout('components.layouts.admin');
    }
}
