<?php

namespace App\Http\Resources\DanaDesa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DanaDesaDetail extends JsonResource
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
            'file' => $this->file,
            'link' => $this->link,
            'date' => $this->date,
        ];
    }
}
