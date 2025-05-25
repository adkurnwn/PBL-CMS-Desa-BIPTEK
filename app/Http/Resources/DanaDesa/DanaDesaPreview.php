<?php

namespace App\Http\Resources\DanaDesa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DanaDesaPreview extends JsonResource
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
            'views' => $this->views,
            'file' => $this->file,
            'downloads' => $this->downloads,
            'date' => $this->date,
        ];
    }
}
