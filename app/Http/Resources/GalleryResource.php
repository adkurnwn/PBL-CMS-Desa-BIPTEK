<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'images' => $this->images->take(5)->map(function ($image) {
                return [
                    'path' => $image->path,
                    'alt' => $image->alt
                ];
            }),
        ];
    }
}
