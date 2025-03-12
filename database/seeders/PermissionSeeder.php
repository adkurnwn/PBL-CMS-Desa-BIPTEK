<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions for each feature
        $features = [
            'users' => ['create', 'read', 'update', 'delete'],
            'positions' => ['create', 'read', 'update', 'delete'],
            'roles' => ['manage'],
            'dashboard' => ['view'],
            'profiles' => ['create', 'read', 'update', 'delete'],
            'news' => ['create', 'read', 'update', 'delete'],
        ];

        foreach ($features as $feature => $actions) {
            foreach ($actions as $action) {
                Permission::create(['name' => $action . '_' . $feature]);
            }
        }

        // Get roles
        $admin = Role::where('name', 'admin')->first();

        // Give admin all permissions by default
        $admin->givePermissionTo(Permission::all());
    }
}
