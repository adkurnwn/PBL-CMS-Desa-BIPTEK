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
            'Pemerintah Desa Mengadakan Pelatihan Kewirausahaan Digital untuk Warga Desa',
            'Kegiatan Sosialisasi Program Desa',
            'Pembangunan Jalan Desa Selesai Tepat Waktu',
            'Renovasi Irigasi Desa Meningkatkan Produktivitas Pertanian',
        ];

        $images = [1, 2, 3, 4];
        $isDraft = [false, false, false, true];
        $x = 0;

        foreach ($newsTitles as $title) {
            $news = News::create([
                'title' => $title,
                'slug' => Str::of($title)->slug('-'),
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                'is_draft' => $isDraft[$x],
                'views' => rand(10, 500),
                'category_id' => $images[$x],
                'user_id' => 1,
                'video' => $x == 2 ? 'news-videos/1.mp4' : null,
            ]);
            $news->images()->create([
                'path' => '/news-images/' . $images[$x] . '.webp',
                'alt' => 'Image ' . $title,
            ]);
            $x++;
        }
    }
}
