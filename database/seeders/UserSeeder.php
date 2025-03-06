<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('Admin');

        $user = User::create([
            'name' => 'Kepala Desa',
            'email' => 'kepala@gmail.com',
            'password' => Hash::make('password'),
            'id_position' => 1,
        ]);

        $user->assignRole('Kepala Desa');
    }
}
