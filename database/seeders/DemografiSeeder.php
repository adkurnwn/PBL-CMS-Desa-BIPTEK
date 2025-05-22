<?php

namespace Database\Seeders;

use App\Models\Demografi;
use Illuminate\Database\Seeder;

class DemografiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate demographic data for 5 years (2019-2023)
        $startYear = 2019;
        $endYear = 2023;
        
        // Base population data that will increase each year
        $basePendudukPria = 2450;
        $basePendudukWanita = 2580;
        $baseJumlahKeluarga = 1250;
        
        // Create demographic data for each year
        for ($year = $startYear; $year <= $endYear; $year++) {
            // Increment population by 2-4% each year
            $growthRate = 1 + (rand(20, 40) / 1000);
            $pendudukPria = (int)($basePendudukPria * $growthRate);
            $pendudukWanita = (int)($basePendudukWanita * $growthRate);
            $jumlahPenduduk = $pendudukPria + $pendudukWanita;
            $jumlahKeluarga = (int)($baseJumlahKeluarga * $growthRate);
            
            // Update base values for next year
            $basePendudukPria = $pendudukPria;
            $basePendudukWanita = $pendudukWanita;
            $baseJumlahKeluarga = $jumlahKeluarga;
            
            // Age distribution (percentages of total population)
            $jumlahAnak = (int)($jumlahPenduduk * rand(20, 25) / 100);  // 20-25%
            $jumlahRemaja = (int)($jumlahPenduduk * rand(15, 20) / 100); // 15-20%
            $jumlahDewasa = (int)($jumlahPenduduk * rand(40, 45) / 100); // 40-45%
            $jumlahLansia = $jumlahPenduduk - $jumlahAnak - $jumlahRemaja - $jumlahDewasa;
            
            // Education distribution (percentages of population over 18)
            $adultPopulation = $jumlahDewasa + $jumlahLansia;
            $tamatPt = (int)($adultPopulation * rand(10, 15) / 100);     // 10-15%
            $tamatSma = (int)($adultPopulation * rand(30, 35) / 100);    // 30-35%
            $tamatSmp = (int)($adultPopulation * rand(25, 30) / 100);    // 25-30%
            $tamatSd = $adultPopulation - $tamatPt - $tamatSma - $tamatSmp;
            
            // Other statistics
            $jumlahPesertaBpjs = (int)($jumlahPenduduk * rand(70, 85) / 100);  // 70-85%
            $jumlahPendatang = (int)($jumlahPenduduk * rand(2, 5) / 100);     // 2-5%
            $jumlahPindahKeluar = (int)($jumlahPenduduk * rand(1, 3) / 100);  // 1-3%
            
            // Create demographic record
            Demografi::create([
                'user_id' => 1,  // Assuming user ID 1 is an admin
                'tahun' => $year,
                'penduduk_pria' => $pendudukPria,
                'penduduk_wanita' => $pendudukWanita,
                'jumlah_penduduk' => $jumlahPenduduk,
                'jumlah_keluarga' => $jumlahKeluarga,
                'jumlah_anak' => $jumlahAnak,
                'jumlah_remaja' => $jumlahRemaja,
                'jumlah_dewasa' => $jumlahDewasa,
                'jumlah_lansia' => $jumlahLansia,
                'tamat_pt' => $tamatPt,
                'tamat_sma' => $tamatSma,
                'tamat_smp' => $tamatSmp,
                'tamat_sd' => $tamatSd,
                'jumlah_peserta_bpjs' => $jumlahPesertaBpjs,
                'jumlah_pendatang' => $jumlahPendatang,
                'jumlah_pindah_keluar' => $jumlahPindahKeluar,
            ]);
        }
    }
}
