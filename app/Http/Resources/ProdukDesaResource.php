<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProdukDesaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->product_name,
            'price' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'image' => [
                'path' => $this->images->first()->path ?? null,
            ],
        ];
    }
}
