<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newsTitles = [
            'Pemerintah Desa Mengadakan Pelatihan',
            'Kegiatan Sosialisasi Program Desa',
            'Pembangunan Jalan Desa Selesai Tepat Waktu',
            'Pemerintah Desa Mengadakan Rapat Umum',
            'Pemerintah Desa Mengadakan Pelatihan Keterampilan',
            'Kegiatan Gotong Royong Masyarakat Desa',
            'Pemerintah Desa Mengadakan Lomba Kebersihan',
        ];

        $images = [1, 2, 3, 4, 5, 6, 7];

        $x = 0;

        foreach ($newsTitles as $title) {
            $news = News::create([
                'title' => $title,
                'slug' => Str::of($title)->slug('-'),
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'is_draft' => false,
                'views' => 0,
                'category_id' => rand(1, 4),
                'user_id' => 1,
            ]);
            $news->images()->create([
                'path' => '/news-images/' . $images[$x] . '.jpg',
                'alt' => 'Image ' . $title,
            ]);
            $x++;
        }
    }
}
