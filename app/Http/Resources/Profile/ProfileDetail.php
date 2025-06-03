<?php

namespace App\Http\Resources\Profile;

use App\Models\User;
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
            'kode_pos' => $this->kode_pos,
            'hero_image' => json_decode($this->hero_images)[1],
            'history' => $this->history,
            'vision' => $this->vision,
            'mission' => $this->mission,
            'fasilitas' => $this->fasilitas,
            'lembaga' => $this->lembaga,
            'organization_image' => $this->organization_image,
            'kepala_desa_name' => User::whereHas('roles', function ($query) {
                $query->where('name', 'Kepala Desa');
            })->where('status', true)->value('name') ?? null,
            'kepala_desa_image' => User::whereHas('roles', function ($query) {
                $query->where('name', 'Kepala Desa');
            })->where('status', true)->value('photo_profile') ?? null,
        ];
    }
}
