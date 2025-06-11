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
            'title' => 'Kegiatan Gotong Royong',
            'slug' => Str::of('Kegiatan Gotong Royong')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Dokumentasi kegiatan gotong royong warga desa',
        ]);

        $images1 = [1, 2, 3];
        foreach ($images1 as $image) {
            $gallery1->images()->create([
                'path' => '/gallery-images/' . $image . '.webp',
                'alt' => 'Kegiatan Gotong Royong ' . $image,
            ]);
        }

        // Second Gallery
        $gallery2 = Gallery::create([
            'title' => 'Pemandangan Alam Desa',
            'slug' => Str::of('Pemandangan Alam Desa')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Koleksi foto pemandangan alam indah di sekitar desa',
        ]);

        $images2 = [4, 5, 6];
        foreach ($images2 as $image) {
            $gallery2->images()->create([
                'path' => '/gallery-images/' . $image . '.webp',
                'alt' => 'Pemandangan Alam ' . $image,
            ]);
        }

        // Third Gallery
        $gallery3 = Gallery::create([
            'title' => 'Pelatihan Keterampilan',
            'slug' => Str::of('Pelatihan Keterampilan')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Dokumentasi pelatihan keterampilan untuk warga desa',
        ]);

        $images3 = [7, 8, 9];
        foreach ($images3 as $image) {
            $gallery3->images()->create([
                'path' => '/gallery-images/' . $image . '.webp',
                'alt' => 'Pelatihan Keterampilan ' . $image,
            ]);
        }

        // Fourth Gallery
        $gallery4 = Gallery::create([
            'title' => 'Fasilitas Desa',
            'slug' => Str::of('Fasilitas Desa')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Dokumentasi fasilitas umum yang ada di desa',
        ]);

        $images4 = [10, 11, 12];
        foreach ($images4 as $image) {
            $gallery4->images()->create([
                'path' => '/gallery-images/' . $image . '.webp',
                'alt' => 'Fasilitas Desa ' . $image,
            ]);
        }

        // Fifth Gallery
        $gallery5 = Gallery::create([
            'title' => 'Festival Budaya Desa',
            'slug' => Str::of('Festival Budaya Desa')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Dokumentasi festival budaya tahunan desa',
        ]);

        $images5 = [13, 14, 15];
        foreach ($images5 as $image) {
            $gallery5->images()->create([
                'path' => '/gallery-images/' . $image . '.webp',
                'alt' => 'Festival Budaya ' . $image,
            ]);
        }

        // Sixth Gallery
        $gallery6 = Gallery::create([
            'title' => 'Pertanian dan Perkebunan',
            'slug' => Str::of('Pertanian dan Perkebunan')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Dokumentasi aktivitas pertanian dan perkebunan desa',
        ]);

        $images6 = [16, 17, 18];
        foreach ($images6 as $image) {
            $gallery6->images()->create([
                'path' => '/gallery-images/' . $image . '.webp',
                'alt' => 'Pertanian dan Perkebunan ' . $image,
            ]);
        }
    }
}
