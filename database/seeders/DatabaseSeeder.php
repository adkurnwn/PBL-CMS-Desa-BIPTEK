<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\DanaDesa;
use App\Models\Demografi;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Define environment-specific seeders
        $environmentSeeders = [
            'production' => [
                RoleSeeder::class,
                PositionSeeder::class,
                PermissionSeeder::class,
                IndoRegionSeeder::class,
                CategorySeeder::class,
            ],
            'installation' => [
                RoleSeeder::class,
                PositionSeeder::class,
                PermissionSeeder::class,
                IndoRegionSeeder::class,
                CategorySeeder::class,
                GallerySeeder::class,
                NewsSeeder::class,
                DanaDesaSeeder::class,
                ProdukHukumSeeder::class,
            ],
            'default' => [
                RoleSeeder::class,
                PositionSeeder::class,
                UserSeeder::class,
                PermissionSeeder::class,
                IndoRegionSeeder::class,
                ProfileSeeder::class,
                CategorySeeder::class,
                EventSeeder::class,
                GallerySeeder::class,
                NewsSeeder::class,
                ProdukDesaSeeder::class,
                DanaDesaSeeder::class,
                DemografiSeeder::class,
                ProdukHukumSeeder::class,
            ]
        ];

        // Get the current environment or use default
        $environment = app()->environment();
        $seeders = $environmentSeeders[$environment] ?? $environmentSeeders['default'];

        // Run the appropriate seeders
        $this->call($seeders);

        $this->command->info("Running seeders for environment: {$environment}");
    }
}
