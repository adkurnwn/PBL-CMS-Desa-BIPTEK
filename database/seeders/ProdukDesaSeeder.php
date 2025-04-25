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
        $produkDesa = ['Produk 1', 'Produk 2', 'Produk 3'];
        $images = [1, 2, 3,];
        $i = 1;
        foreach ($produkDesa as $produk) {
            $produkDesa = ProdukDesa::create([
                'name' => $produk,
                'slug' => Str::of($produk)->slug('-'),
                'user_id' => 1,
                'kontak_penjual' => '08123456789',
                'description' => 'Description for ' . $produk,
                'price' => rand(10000, 100000),
                'stock' => rand(1, 100),
            ]);
            $produkDesa->images()->create([
                'path' => '/produk-desa-images/' . $i . '.jpg',
            ]);
            $i++;
        }
    }
}
