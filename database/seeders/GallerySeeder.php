<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First Gallery
        $gallery1 = Gallery::create([
            'title' => 'Kegiatan Desa',
            'slug' => Str::of('Kegiatan Desa')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Dokumentasi berbagai kegiatan yang dilaksanakan di desa',
        ]);

        $images1 = [1, 2, 3, 4, 5];

        foreach ($images1 as $image) {
            $gallery1->images()->create([
                'path' => '/gallery-images/kegiatan-' . $image . '.jpg',
                'alt' => 'Kegiatan Desa ' . $image,
            ]);
        }

        // Second Gallery
        $gallery2 = Gallery::create([
            'title' => 'Pemandangan Desa',
            'slug' => Str::of('Pemandangan Desa')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Koleksi foto pemandangan indah dan fasilitas desa',
        ]);

        $images2 = [6, 7, 8, 9, 10];

        foreach ($images2 as $image) {
            $gallery2->images()->create([
                'path' => '/gallery-images/pemandangan-' . $image . '.jpg',
                'alt' => 'Pemandangan Desa ' . $image,
            ]);
        }
    }
}
