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
            'profiles' => ['read', 'update'],
            'news' => ['create', 'read', 'update', 'archive', 'restore', 'delete'],
            'galleries' => ['create', 'read', 'update', 'delete'],
            'events' => ['create', 'read', 'update', 'delete'],
            'produk-hukum' => ['create', 'read', 'update', 'delete'],
            'dana-desa' => ['create', 'read', 'update', 'delete'],
            'produk-desa' => ['create', 'read', 'update', 'delete'],
            'comments' => ['read', 'update', 'delete'],
            'demografi' => ['create', 'read', 'update'],
            'feedbacks' => ['create', 'read', 'update'],
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
