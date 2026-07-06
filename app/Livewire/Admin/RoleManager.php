<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    use \Livewire\WithPagination;

    public string $search = '';
    public string $sortField = 'id';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    // Modal state
    public ?int $roleId = null;
    public string $name = '';
    /** @var array<int, string> */
    public array $selectedPermissions = [];

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

    public function createRole(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.create');

        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        $this->reset(['name', 'selectedPermissions']);
        \Flux::modal('role-modal')->close();
        $this->dispatch('notify', message: 'Role created successfully!');
    }

    public function editRole(int $id): void
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.edit');

        /** @var Role $role */
        $role = Role::findOrFail($id);
        
        if ($role->name === 'Root') {
            $this->dispatch('notify', message: 'Cannot edit the Root role.', type: 'error');
            return;
        }

        $this->roleId = (int) $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();

        \Flux::modal('role-modal')->show();
    }

    public function updateRole(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.edit');

        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
        ]);

        /** @var Role $role */
        $role = Role::findOrFail($this->roleId);
        
        if ($role->name === 'Root') {
            $this->dispatch('notify', message: 'Cannot modify the Root role.', type: 'error');
            return;
        }

        $role->name = $this->name;
        $role->save();
        
        $role->syncPermissions($this->selectedPermissions);

        \Flux::modal('role-modal')->close();
        $this->dispatch('notify', message: 'Role updated successfully!');
    }

    public function confirmDelete(int $id): void
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.delete');

        $this->roleId = $id;
        \Flux::modal('delete-role-modal')->show();
    }

    public function deleteRole(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.delete');

        /** @var Role $role */
        $role = Role::findOrFail($this->roleId);
        if ($role->name === 'Root') {
            $this->dispatch('notify', message: 'Cannot delete the Root role.', type: 'error');
            return;
        }

        $role->delete();

        \Flux::modal('delete-role-modal')->close();
        $this->dispatch('notify', message: 'Role deleted successfully!');
    }

    public function openNewRoleModal(): void
    {
        $this->reset(['roleId', 'name', 'selectedPermissions']);
        \Flux::modal('role-modal')->show();
    }

    public function render(): \Illuminate\View\View
    {
        $roles = Role::with('permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.role-manager', [
            'roles' => $roles,
            'allPermissions' => \Spatie\Permission\Models\Permission::all(),
        ])->layout('components.layouts.admin');
    }
}
