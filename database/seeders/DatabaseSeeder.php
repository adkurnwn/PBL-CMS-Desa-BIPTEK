<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\DanaDesa;
use App\Models\Demografi;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                RoleSeeder::class,
                PositionSeeder::class,
                PermissionSeeder::class,
                CategorySeeder::class,
                GallerySeeder::class,
                NewsSeeder::class,
                DanaDesaSeeder::class,
                ProdukHukumSeeder::class,
            ]
        );
    }
}
