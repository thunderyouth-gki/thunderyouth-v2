<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    // Modal state
    public ?int $permissionId = null;

    public string $name = '';

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

    public function createPermission(): void
    {
        Gate::authorize('permissions.create');

        $this->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $this->name]);

        $this->reset(['name']);
        \Flux::modal('permission-modal')->close();
        $this->dispatch('notify', message: 'Permission created successfully!');
    }

    public function editPermission(int $id): void
    {
        Gate::authorize('permissions.edit');

        /** @var Permission $permission */
        $permission = Permission::findOrFail($id);
        $this->permissionId = (int) $permission->id;
        $this->name = $permission->name;

        \Flux::modal('permission-modal')->show();
    }

    public function updatePermission(): void
    {
        Gate::authorize('permissions.edit');

        $this->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$this->permissionId,
        ]);

        /** @var Permission $permission */
        $permission = Permission::findOrFail($this->permissionId);
        $permission->name = $this->name;
        $permission->save();

        \Flux::modal('permission-modal')->close();
        $this->dispatch('notify', message: 'Permission updated successfully!');
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('permissions.delete');

        $this->permissionId = $id;
        \Flux::modal('delete-permission-modal')->show();
    }

    public function deletePermission(): void
    {
        Gate::authorize('permissions.delete');

        /** @var Permission $permission */
        $permission = Permission::findOrFail($this->permissionId);
        $permission->delete();

        \Flux::modal('delete-permission-modal')->close();
        $this->dispatch('notify', message: 'Permission deleted successfully!');
    }

    public function openNewPermissionModal(): void
    {
        $this->reset(['permissionId', 'name']);
        \Flux::modal('permission-modal')->show();
    }

    public function render(): View
    {
        $permissions = Permission::with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.permission-manager', [
            'permissions' => $permissions,
        ])->layout('components.layouts.admin');
    }
}
