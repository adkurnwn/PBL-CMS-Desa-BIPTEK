<?php

namespace App\Http\Resources\ProdukHukum;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukHukumPreview extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category->name,
            'views' => $this->views,
            'file' => $this->file,
            'downloads' => $this->downloads,
            'date' => $this->date,
        ];
    }
}
