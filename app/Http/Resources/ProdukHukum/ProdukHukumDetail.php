<?php

namespace App\Http\Resources\ProdukHukum;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukHukumDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category->name,
            'file' => $this->file,
            'link' => $this->link,
            'date' => $this->date,
            'is_moderate' => $this->is_moderate,
        ];
    }
}
