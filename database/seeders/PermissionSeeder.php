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
            'home',
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
            'statistics',
        ];

        foreach ($features as $feature) {
            Permission::create(['name' => 'manage' . '_' . $feature]);
        }

        // Get roles
        $admin = Role::where('name', 'Admin')->first();
        $kepalaDesa = Role::where('name', 'Kepala Desa')->first();
        $aparatDesa = Role::where('name', 'Aparat Desa')->first();

        // Give admin all permissions by default
        $admin->givePermissionTo(Permission::all());
        $kepalaDesa->givePermissionTo([
            'manage_home',
            'manage_demografi',
            'manage_statistics',
            'manage_feedbacks',
        ]);
        $aparatDesa->givePermissionTo([
            'manage_home',
            'manage_news',
            'manage_galleries',
            'manage_events',
            'manage_produk-hukum',
            'manage_dana-desa',
            'manage_produk-desa',
            'manage_feedbacks',
            'manage_comments',
        ]);
    }
}
