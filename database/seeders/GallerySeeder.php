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
        $gallery = Gallery::create([
            'title' => 'Gallery',
            'slug' => Str::of('Gallery')->slug('-'),
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Gallery',
        ]);

        $images = [1, 2, 3, 4, 5];

        foreach ($images as $image) {
            $gallery->images()->create([
                'path' => '/gallery-images/' . $image . '.jpg',
                'alt' => 'Image ' . $image,
            ]);
        }
    }
}
