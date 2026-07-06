<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserAccessManager extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal state
    public $manageUserId = null;
    public $deleteUserId = null;
    public $selectedRole = '';
    public $selectedPermissions = [];

    // New User state
    public $newName = '';
    public $newEmail = '';
    public $newPassword = '';

    // Edit User state
    public $editUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editPassword = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openManageModal($userId)
    {
        $this->manageUserId = $userId;
        $user = User::findOrFail($userId);
        $this->manageUserId = $user->id;
        $this->selectedRole = $user->roles->first()?->name ?? '';
        $this->selectedPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        \Flux::modal('manage-user-modal')->show();
    }

    public function saveAccess()
    {
        $user = User::findOrFail($this->manageUserId);
        
        if ($user->hasRole('Root')) {
            \Flux::modal('manage-user-modal')->close();
            $this->dispatch('notify', message: 'Cannot modify Root user access.');
            return;
        }

        if ($this->selectedRole) {
            $user->syncRoles([$this->selectedRole]);
        } else {
            $user->syncRoles([]);
        }
        
        $user->syncPermissions($this->selectedPermissions);
        
        \Flux::modal('manage-user-modal')->close();
        $this->dispatch('notify', message: 'User access updated successfully!');
    }

    public function openNewUserModal()
    {
        $this->reset(['newName', 'newEmail', 'newPassword']);
        \Flux::modal('new-user-modal')->show();
    }

    public function createUser()
    {
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

    public function openEditUserModal($userId)
    {
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

    public function updateUser()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->editUserId,
            'editPassword' => 'nullable|string|min:8',
        ]);

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

    public function toggleActiveStatus($userId)
    {
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

    public function confirmDelete($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->hasRole('Root')) {
            $this->dispatch('notify', message: 'Cannot delete the Root user.');
            return;
        }
        
        $this->deleteUserId = $userId;
        \Flux::modal('delete-user-modal')->show();
    }

    public function deleteUser()
    {
        if (!$this->deleteUserId) return;

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

    public function render()
    {
        $users = User::with('roles', 'permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.user-access-manager', [
            'users' => $users,
            'allRoles' => \Spatie\Permission\Models\Role::where('name', '!=', 'Root')->get(),
            'allPermissions' => \Spatie\Permission\Models\Permission::all(),
        ])->layout('components.layouts.admin');
    }
}
