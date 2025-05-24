<?php

namespace App\Http\Resources\Gallery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryHighlight extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'images' => $this->images->take(5)->map(function ($image) {
                return [
                    'path' => $image->path,
                    'alt' => $image->alt
                ];
            }),
            'slug' => $this->slug,
        ];
    }
}
