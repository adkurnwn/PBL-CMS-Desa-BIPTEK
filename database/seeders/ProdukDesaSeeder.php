<?php

namespace Database\Seeders;

use App\Models\ProdukDesa;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdukDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produkDesaNames = ['Tanaman Hias', 'Kain Batik', 'Tas Tenun'];
        $images = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $imageIndex = 0;

        foreach ($produkDesaNames as $produk) {
            $produkDesaModel = ProdukDesa::create([
                'product_name' => $produk,
                'slug' => Str::of($produk)->slug('-'),
                'user_id' => 1,
                'nama_penjual' => 'John Doe',
                'kontak_penjual' => '08123456789',
                'alamat_penjual' => 'Jl. Contoh Alamat No. 123',
                'description' => 'Description for ' . $produk,
                'price' => rand(10000, 100000),
                'status' => true,
            ]);

            // Menambahkan 3 gambar untuk setiap produk
            for ($j = 0; $j < 3; $j++) {
                $produkDesaModel->images()->create([
                    'path' => '/produk-desa-images/' . $images[$imageIndex] . '.webp',
                ]);
                $imageIndex++;
            }
        }
    }
}
