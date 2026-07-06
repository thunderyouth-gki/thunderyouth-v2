<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserAccessManager extends Component
{
    public function render()
    {
        return view('livewire.admin.user-access-manager', [
            'users' => User::with('roles', 'permissions')->get(),
        ])->layout('components.layouts.admin');
    }
}
