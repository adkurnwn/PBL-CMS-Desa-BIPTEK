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
            'name' => 'Desa Sumberejo',
            'email' => 'desa.sumberejo@surabaya.go.id',
            'phone' => '031-7405559',
            'logo' => 'defaults/logo/sumberejo.png',
            'provinsi' => '35',
            'kabupaten' => '3578',
            'kecamatan' => '3578120',
            'kelurahan' => '3578120002',
            'address' => 'Jl. Raya Sumberejo No. 1, Kecamatan Pakal, Kota Surabaya',
            'hero_images' => json_encode([
                'defaults/hero/hero-1.jpg',
                'defaults/hero/hero-2.jpg',
                'defaults/hero/hero-3.jpg'
            ]),
            'organization_image' => 'defaults/organization/structure.png',
            'lembaga' => json_encode([
                'BPD (Badan Permusyawaratan Desa)',
                'LPMD (Lembaga Pemberdayaan Masyarakat Desa)',
                'PKK (Pemberdayaan Kesejahteraan Keluarga)',
                'Karang Taruna',
                'Lembaga Adat Desa',
                'RT/RW',
                'Linmas (Perlindungan Masyarakat)',
                'Posyandu'
            ]),
            'fasilitas' => json_encode([
                'Balai Desa',
                'Puskesmas Pembantu',
                'SD Negeri Sumberejo',
                'TK/PAUD',
                'Masjid Jami\' Sumberejo',
                'Lapangan Olahraga',
                'Taman Desa',
                'Pasar Desa',
                'BUMDes (Badan Usaha Milik Desa)',
                'Bank Sampah'
            ]),
            'history' => 'Desa Sumberejo memiliki sejarah panjang yang dimulai sejak tahun 1921. Awalnya merupakan pemukiman kecil yang dihuni oleh beberapa keluarga petani. Nama Sumberejo berasal dari kata "Sumber" yang berarti mata air dan "Rejo" yang berarti makmur, karena daerah ini memiliki sumber mata air yang melimpah dan tanah yang subur untuk pertanian. Seiring berjalannya waktu, desa ini berkembang menjadi desa yang maju dengan berbagai potensi di bidang pertanian, perikanan, dan home industry.',
            'vision' => 'Mewujudkan Desa Sumberejo yang mandiri, maju, sejahtera dan berkelanjutan berbasis pertanian, teknologi, dan kearifan lokal.',
            'mission' => "1. Meningkatkan kualitas pelayanan publik dan tata kelola pemerintahan desa yang transparan
2. Mengembangkan potensi ekonomi desa melalui optimalisasi BUMDes dan sektor pertanian
3. Meningkatkan kualitas sumber daya manusia melalui pendidikan dan pelatihan
4. Membangun infrastruktur desa yang berkualitas dan ramah lingkungan
5. Memperkuat nilai-nilai sosial, budaya, dan kearifan lokal dalam kehidupan masyarakat",
            'primary_color' => '#000000',
            'secondary_color' => '#000000',
        ]);
    }
}
