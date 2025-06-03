<?php

namespace Database\Seeders;

use App\Models\DanaDesa;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DanaDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Laporan Dana Desa Tahun 2024',
            'Anggaran Dana Desa Triwulan I 2024',
            'Realisasi Dana Desa Tahun 2023',
        ];

        // Create directory if it doesn't exist
        $storagePath = storage_path('app/public/dana-desa-documents');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Create sample data
        foreach ($titles as $index => $title) {
            $views = rand(15, 500);
            $downloads = rand(5, $views);

            DanaDesa::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'user_id' => rand(1, 3),
                'file' => null,
                'link' => $index % 2 != 0 ? 'https://example.com/dana-desa/document-' . ($index + 1) : null,
                'date' => now()->subDays(rand(1, 365))->format('Y-m-d'),
                'views' => $views,
                'downloads' => $downloads,
            ]);
        }
    }
}
