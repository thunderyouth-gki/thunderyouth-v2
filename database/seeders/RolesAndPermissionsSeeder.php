<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions using resource.action convention
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles and assign permissions
        $rootRole = Role::findOrCreate('Root');
        // Root bypasses all permission checks via Gate::before, no need to assign explicitly.

        $pengurusRole = Role::findOrCreate('Pengurus');
        $pengurusRole->givePermissionTo([
            'users.view',
            'users.create',
            'users.edit',
        ]);

        $jemaatRole = Role::findOrCreate('Jemaat');
        // Jemaat might have other basic permissions later
    }
}
