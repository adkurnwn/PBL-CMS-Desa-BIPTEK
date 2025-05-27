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
            ],
            [
                'title' => 'Peraturan Bupati No. 15 Tahun 2023 tentang Pembinaan Desa',
                'description' => 'Peraturan Bupati tentang pedoman pembinaan dan pengawasan penyelenggaraan pemerintahan desa',
                'date' => '2023-04-05',
            ],
            [
                'title' => 'SK Kepala Desa No. 12 Tahun 2023 tentang Pengangkatan Perangkat Desa',
                'description' => 'Surat Keputusan tentang pengangkatan perangkat desa untuk mengisi jabatan kosong',
                'date' => '2023-05-12',
            ],
            [
                'title' => 'Peraturan Desa No. 4 Tahun 2023 tentang Pelaksanaan Pembangunan Desa',
                'description' => 'Peraturan mengenai mekanisme dan tata cara pelaksanaan pembangunan di wilayah desa',
                'date' => '2023-06-18',
            ],
            [
                'title' => 'Peraturan Bupati No. 22 Tahun 2023 tentang Dana Desa',
                'description' => 'Peraturan Bupati tentang tata cara pengalokasian, penyaluran, dan penggunaan dana desa',
                'date' => '2023-07-02',
            ],
            [
                'title' => 'Peraturan Gubernur No. 45 Tahun 2023 tentang Pelestarian Lingkungan',
                'description' => 'Peraturan Gubernur mengenai upaya pelestarian lingkungan hidup di wilayah pedesaan',
                'date' => '2023-07-25',
            ],
            [
                'title' => 'Peraturan Desa No. 5 Tahun 2023 tentang Pembentukan RT/RW',
                'description' => 'Peraturan mengenai pembentukan, pembagian wilayah, dan pengelolaan RT/RW',
                'date' => '2023-08-14',
            ],
            [
                'title' => 'Peraturan Desa No. 6 Tahun 2023 tentang Pengelolaan Aset Desa',
                'description' => 'Peraturan tentang tata kelola, inventarisasi, dan pemanfaatan aset desa',
                'date' => '2023-09-22',
            ],
            [
                'title' => 'Peraturan Bupati No. 30 Tahun 2023 tentang Administrasi Desa',
                'description' => 'Peraturan mengenai tata cara administrasi pemerintahan desa yang efektif dan efisien',
                'date' => '2023-10-09',
            ],
            [
                'title' => 'Peraturan Desa No. 7 Tahun 2023 tentang Penanggulangan Bencana',
                'description' => 'Peraturan mengenai penanggulangan bencana dan tanggap darurat di wilayah desa',
                'date' => '2023-11-17',
            ],
        ];

        foreach ($produkHukumData as $index => $data) {
            // Generate views and downloads
            $views = rand(50, 800);
            $downloads = rand(20, $views);

            // Determine if document has a link or file
            $hasFile = $index % 2 == 0;
            $hasLink = !$hasFile && ($index % 3 == 0);

            // Create mock file name if needed
            $filename = null;
            if ($hasFile) {
                $filename = 'dokumen-' . ($index + 1) . '.pdf';
            }

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
                'user_id' => rand(1, 3),
                'file' => $hasFile ? 'produk-hukum-documents/' . $filename : null,
                'link' => $hasLink ? 'https://drive.google.com/file/example/produk-hukum-' . ($index + 1) : null,
                'date' => $data['date'],
                'is_moderate' => $isModerated,
                'views' => $views,
                'downloads' => $downloads,
            ]);
        }
    }
}
