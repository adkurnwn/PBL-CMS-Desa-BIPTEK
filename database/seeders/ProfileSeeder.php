<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profile::create([
            'name' => 'Desa Example',
            'email' => 'example@desa.gov.id',
            'phone' => '0812345678',
            'logo' => 'default/logo.png',
            'provinsi' => '35',
            'kabupaten' => '3578',
            'kecamatan' => '357801',
            'kelurahan' => '3578012001',
            'hero_image' => 'default/hero.jpg',
            'organization_image' => 'default/organization.jpg',
            'lembaga' => json_encode([
                'BPD (Badan Permusyawaratan Desa)',
                'LPMD (Lembaga Pemberdayaan Masyarakat Desa)',
                'PKK (Pemberdayaan Kesejahteraan Keluarga)',
                'Karang Taruna'
            ]),
            'fasilitas' => json_encode([
                'Kantor Desa',
                'Puskesmas',
                'Sekolah Dasar',
                'Masjid'
            ]),
            'history' => 'Sejarah Desa Example dimulai pada tahun 1945...',
            'vision' => 'Mewujudkan Desa Example yang maju, mandiri dan sejahtera',
            'mission' => 'Meningkatkan pelayanan masyarakat, Mengembangkan potensi desa, Membangun infrastruktur desa',
            'primary_color' => '#000000',
            'secondary_color' => '#ffffff',
        ]);
    }
}
