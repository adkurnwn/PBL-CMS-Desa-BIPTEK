<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Kepala Desa',
                'job_description' => json_encode([
                    'Memimpin penyelenggaraan pemerintahan desa',
                    'Mengembangkan perekonomian desa',
                    'Mengkoordinasikan pembangunan desa secara partisipatif',
                    'Mewakili desanya di dalam dan di luar pengadilan',
                    'Menetapkan peraturan desa yang telah disetujui bersama BPD'
                ])
            ],
            [
                'name' => 'Sekretaris Desa',
                'job_description' => json_encode([
                    'Melaksanakan urusan ketatausahaan',
                    'Melaksanakan urusan umum',
                    'Melaksanakan urusan keuangan',
                    'Melaksanakan urusan perencanaan',
                    'Membantu kepala desa dalam administrasi pemerintahan'
                ])
            ],
            [
                'name' => 'Bendahara Desa',
                'job_description' => json_encode([
                    'Mengelola keuangan desa',
                    'Menyusun laporan keuangan desa',
                    'Membantu kepala desa dalam melaksanakan pengelolaan keuangan desa',
                    'Bertanggung jawab terhadap pengeluaran dan pemasukan keuangan desa'
                ])
            ],
            [
                'name' => 'Kaur Pemerintahan',
                'job_description' => json_encode([
                    'Melaksanakan administrasi kependudukan',
                    'Mempersiapkan bahan-bahan penyusunan rancangan peraturan desa',
                    'Melaksanakan administrasi pertanahan',
                    'Mempersiapkan bantuan hukum dan HAM'
                ])
            ],
            [
                'name' => 'Kaur Pembangunan',
                'job_description' => json_encode([
                    'Melaksanakan pembangunan sarana prasarana desa',
                    'Mengelola pemeliharaan sarana prasarana desa',
                    'Melaksanakan pembinaan perekonomian desa',
                    'Mengelola administrasi pembangunan desa'
                ])
            ],
            [
                'name' => 'Kaur Kesejahteraan',
                'job_description' => json_encode([
                    'Melaksanakan program pemberdayaan masyarakat',
                    'Melaksanakan program kesehatan masyarakat',
                    'Melaksanakan program pendidikan dan kebudayaan',
                    'Mengelola kegiatan sosial dan keagamaan'
                ])
            ],
            [
                'name' => 'Kepala Dusun',
                'job_description' => json_encode([
                    'Membantu kepala desa dalam menjalankan tugas di wilayahnya',
                    'Melaksanakan kegiatan pemerintahan, pembangunan, dan pembinaan kemasyarakatan',
                    'Melaksanakan keputusan dan kebijakan kepala desa',
                    'Membina ketentraman dan ketertiban masyarakat'
                ])
            ],
            [
                'name' => 'Admin Sistem',
                'job_description' => json_encode([
                    'Mengelola website dan sistem informasi desa',
                    'Melakukan backup data secara berkala',
                    'Memelihara keamanan sistem dan data',
                    'Melakukan pembaruan sistem sesuai kebutuhan'
                ])
            ]
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
