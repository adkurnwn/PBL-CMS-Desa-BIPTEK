<?php

namespace App\Http\Resources\ProdukDesa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukDesaPreview extends JsonResource
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
            'slug' => $this->slug,
            'price' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'thumbnail' =>  $this->images->first()->path ?? null,
            'alt' => $this->images->first()->alt ?? null,
        ];
    }
}
