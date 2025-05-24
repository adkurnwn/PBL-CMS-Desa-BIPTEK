<?php

namespace App\Http\Resources\ProdukDesa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukDesaDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->product_name,
            'price' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'alamat' => $this->alamat_penjual,
            'phone' => $this->kontak_penjual,
            'description' => $this->description,
            'images' => $this->images,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'x' => $this->x,
            'tiktok' => $this->tiktok,
            'youtube' => $this->youtube,
        ];
    }
}
