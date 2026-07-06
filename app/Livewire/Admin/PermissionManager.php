<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionManager extends Component
{
    public function render()
    {
        return view('livewire.admin.permission-manager', [
            'permissions' => Permission::all(),
        ])->layout('components.layouts.admin');
    }
}
