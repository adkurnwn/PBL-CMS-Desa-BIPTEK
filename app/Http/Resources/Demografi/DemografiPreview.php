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
            'penduduk' => $this->jumlah_penduduk,
            'kepala_keluarga' => $this->jumlah_keluarga,
            'penduduk_laki_laki' => $this->penduduk_pria,
            'penduduk_perempuan' => $this->penduduk_wanita,
        ];
    }
}
