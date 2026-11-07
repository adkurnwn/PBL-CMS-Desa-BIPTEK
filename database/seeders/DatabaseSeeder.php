<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            IndoRegionSeeder::class,

            ProfileSeeder::class,

            PositionSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,

            UserSeeder::class,

            CategorySeeder::class,

            GallerySeeder::class,
            NewsSeeder::class,
            DanaDesaSeeder::class,
            ProdukHukumSeeder::class,
            ProdukDesaSeeder::class,
            EventSeeder::class,
            DemografiSeeder::class,
        ]);
    }
}
