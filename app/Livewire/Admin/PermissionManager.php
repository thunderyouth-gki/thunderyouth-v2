<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionManager extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal state
    public $permissionId = null;
    public $name = '';

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

    public function createPermission()
    {
        \Illuminate\Support\Facades\Gate::authorize('permissions.create');

        $this->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $this->name]);

        $this->reset(['name']);
        \Flux::modal('permission-modal')->close();
        $this->dispatch('notify', message: 'Permission created successfully!');
    }

    public function editPermission($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('permissions.edit');

        $permission = Permission::findOrFail($id);
        $this->permissionId = $permission->id;
        $this->name = $permission->name;

        \Flux::modal('permission-modal')->show();
    }

    public function updatePermission()
    {
        \Illuminate\Support\Facades\Gate::authorize('permissions.edit');

        $this->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $this->permissionId,
        ]);

        $permission = Permission::findOrFail($this->permissionId);
        $permission->name = $this->name;
        $permission->save();

        \Flux::modal('permission-modal')->close();
        $this->dispatch('notify', message: 'Permission updated successfully!');
    }

    public function confirmDelete($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('permissions.delete');

        $this->permissionId = $id;
        \Flux::modal('delete-permission-modal')->show();
    }

    public function deletePermission()
    {
        \Illuminate\Support\Facades\Gate::authorize('permissions.delete');

        $permission = Permission::findOrFail($this->permissionId);
        $permission->delete();

        \Flux::modal('delete-permission-modal')->close();
        $this->dispatch('notify', message: 'Permission deleted successfully!');
    }

    public function openNewPermissionModal()
    {
        $this->reset(['permissionId', 'name']);
        \Flux::modal('permission-modal')->show();
    }

    public function render()
    {
        $permissions = Permission::with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.permission-manager', [
            'permissions' => $permissions,
        ])->layout('components.layouts.admin');
    }
}
