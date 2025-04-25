<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $newsCategory = ['Edukasi', 'Kesehatan', 'Pendidikan', 'Kegiatan'];
        foreach ($newsCategory as $category) {
            Category::create([
                'name' => $category,
                'category_type' => 1,
            ]);
        }

        $produkHukumCategory = ['Peraturan Desa', 'Peraturan Bupati', 'Peraturan Gubernur'];
        foreach ($produkHukumCategory as $category) {
            Category::create([
                'name' => $category,
                'category_type' => 0,
            ]);
        }
    }
}
