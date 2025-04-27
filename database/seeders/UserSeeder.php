<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get position IDs
        $adminPosition = Position::where('name', 'Admin Sistem')->first();
        $kepalaDesaPosition = Position::where('name', 'Kepala Desa')->first();

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'position_id' => null,
            'phone' => '081234567890',
            'status' => true,
        ]);

        $admin->assignRole('Admin');

        // Create kepala desa user
        $kepalaDesa = User::create([
            'name' => 'Kepala Desa',
            'email' => 'kepala@gmail.com',
            'password' => Hash::make('password'),
            'position_id' => $kepalaDesaPosition->id,
            'photo_profile' => '/profile-photos/kepala-desa.png',
            'phone' => '089876543210',
            'status' => true,
        ]);

        $kepalaDesa->assignRole('Kepala Desa');

        // Create sekretaris desa user
        $sekretarisPosition = Position::where('name', 'Sekretaris Desa')->first();
        $sekretaris = User::create([
            'name' => 'Sekretaris Desa',
            'email' => 'sekretaris@gmail.com',
            'password' => Hash::make('password'),
            'position_id' => $sekretarisPosition->id,
            'phone' => '081122334455',
            'status' => true,
        ]);

        $sekretaris->assignRole('Aparat Desa');

        // Create bendahara desa user
        $bendaharaPosition = Position::where('name', 'Bendahara Desa')->first();
        $bendahara = User::create([
            'name' => 'Bendahara Desa',
            'email' => 'bendahara@gmail.com',
            'password' => Hash::make('password'),
            'position_id' => $bendaharaPosition->id,
            'phone' => '082233445566',
            'status' => true,
        ]);

        $bendahara->assignRole('Aparat Desa');
    }
}
