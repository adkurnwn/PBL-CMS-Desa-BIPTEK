<?php

namespace Database\Seeders;

use App\Models\ProdukHukum;
use App\Models\Category;
use App\Helpers\SlugHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProdukHukumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create storage directory if it doesn't exist
        $storagePath = storage_path('app/public/produk-hukum-documents');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Get category IDs for produk hukum (category_type = 0)
        $categoryIds = Category::where('category_type', 0)->pluck('id')->toArray();

        $produkHukumData = [
            [
                'title' => 'Peraturan Desa No. 1 Tahun 2023 tentang APBDes',
                'description' => 'Peraturan Desa tentang Anggaran Pendapatan dan Belanja Desa tahun anggaran 2023',
                'date' => '2023-01-15',
            ],
            [
                'title' => 'Peraturan Desa No. 2 Tahun 2023 tentang Pengelolaan Sampah',
                'description' => 'Peraturan yang mengatur tentang pengelolaan sampah di lingkungan Desa Sumberejo',
                'date' => '2023-02-20',
            ],
            [
                'title' => 'Peraturan Desa No. 3 Tahun 2023 tentang BUMDes',
                'description' => 'Peraturan Desa mengenai pembentukan dan pengelolaan Badan Usaha Milik Desa',
                'date' => '2023-03-10',
            ]
        ];

        foreach ($produkHukumData as $index => $data) {
            // Generate views and downloads
            $views = rand(50, 800);
            $downloads = rand(20, $views);

            // Determine if document has a link or file
            $hasFile = $index % 2 == 0;
            $hasLink = !$hasFile && ($index % 3 == 0);

            // Select a random category ID
            $categoryId = $categoryIds[array_rand($categoryIds)];

            // Moderation flag (30% are moderated)
            $isModerated = rand(1, 10) <= 3;

            // Create the produk hukum entry
            ProdukHukum::create([
                'title' => $data['title'],
                'slug' => SlugHelper::generateUniqueSlug(ProdukHukum::class, $data['title']),
                'description' => $data['description'],
                'category_id' => $categoryId,
                'user_id' => 1,
                'file' => $hasFile ? 'produk-hukum-documents/document-dummy.pdf' : null,
                'link' => $hasLink ? 'https://drive.google.com/file/example/produk-hukum-' . ($index + 1) : null,
                'date' => $data['date'],
                'is_moderate' => $isModerated,
                'views' => $views,
                'downloads' => $downloads,
            ]);
        }
    }
}
