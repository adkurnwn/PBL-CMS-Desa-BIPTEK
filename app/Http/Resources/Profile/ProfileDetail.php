<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'hero_image' => json_decode($this->hero_images)[1],
            'history' => $this->history,
            'vision' => $this->vision,
            'mission' => $this->mission,
            'fasilitas' => $this->fasilitas,
            'lembaga' => $this->lembaga,
            'organization_image' => $this->organization_image,
        ];
    }
}
