<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal state
    public $roleId = null;
    public $name = '';
    public $selectedPermissions = [];

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

    public function createRole()
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

    public function editRole($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.edit');

        $role = Role::findOrFail($id);
        
        if ($role->name === 'Root') {
            $this->dispatch('notify', message: 'Cannot edit the Root role.', type: 'error');
            return;
        }

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();

        \Flux::modal('role-modal')->show();
    }

    public function updateRole()
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.edit');

        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
        ]);

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

    public function confirmDelete($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.delete');

        $this->roleId = $id;
        \Flux::modal('delete-role-modal')->show();
    }

    public function deleteRole()
    {
        \Illuminate\Support\Facades\Gate::authorize('roles.delete');

        $role = Role::findOrFail($this->roleId);
        if ($role->name === 'Root') {
            $this->dispatch('notify', message: 'Cannot delete the Root role.', type: 'error');
            return;
        }

        $role->delete();

        \Flux::modal('delete-role-modal')->close();
        $this->dispatch('notify', message: 'Role deleted successfully!');
    }

    public function openNewRoleModal()
    {
        $this->reset(['roleId', 'name', 'selectedPermissions']);
        \Flux::modal('role-modal')->show();
    }

    public function render()
    {
        $roles = Role::with('permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.role-manager', [
            'roles' => $roles,
            'allPermissions' => \Spatie\Permission\Models\Permission::all(),
        ])->layout('components.layouts.admin');
    }
}
