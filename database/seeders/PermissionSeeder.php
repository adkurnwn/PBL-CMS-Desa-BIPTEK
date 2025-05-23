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
            'users',
            'positions',
            'roles',
            'dashboard',
            'profiles',
            'news',
            'galleries',
            'events',
            'produk-hukum',
            'dana-desa',
            'produk-desa',
            'comments',
            'demografi',
            'feedbacks',
            'activity-logs',
            'recovery',
            'settings',
        ];

        foreach ($features as $feature) {
            Permission::create(['name' => 'manage' . '_' . $feature]);
        }

        // Get roles
        $admin = Role::where('name', 'admin')->first();

        // Give admin all permissions by default
        $admin->givePermissionTo(Permission::all());
    }
}
