<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    public function render()
    {
        return view('livewire.admin.role-manager', [
            'roles' => Role::all(),
        ])->layout('components.layouts.admin');
    }
}
