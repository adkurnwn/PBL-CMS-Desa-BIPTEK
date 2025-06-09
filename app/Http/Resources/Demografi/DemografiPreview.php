<?php

namespace App\Http\Resources\Demografi;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemografiPreview extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tahun' => $this->tahun,
            'penduduk_pria' => $this->penduduk_pria,
            'penduduk_wanita' => $this->penduduk_wanita,
            'jumlah_peserta_bpjs' => $this->jumlah_peserta_bpjs,
            'jumlah_pindah_keluar' => $this->jumlah_pindah_keluar,
            'jumlah_pendatang' => $this->jumlah_pendatang,
            'tamat_pt' => $this->tamat_pt,
            'tamat_sma' => $this->tamat_sma,
            'tamat_smp' => $this->tamat_smp,
            'tamat_sd' => $this->tamat_sd,
            'jumlah_lansia' => $this->jumlah_lansia,
            'jumlah_dewasa' => $this->jumlah_dewasa,
            'jumlah_remaja' => $this->jumlah_remaja,
            'jumlah_anak' => $this->jumlah_anak,
            'jumlah_penduduk' => $this->jumlah_penduduk,
            'jumlah_keluarga' => $this->jumlah_keluarga,
        ];
    }
}
