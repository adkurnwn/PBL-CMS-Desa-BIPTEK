<?php

namespace App\Http\Resources\Gallery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryPreview extends JsonResource
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
            'thumbnail' => $this->images->first()->path ?? null,
            'alt' => $this->images->first()->alt ?? null,
            'created_at' => $this->created_at,
        ];
    }
}
