<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileLayout extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'logo' => $this->logo,
            'name' => $this->name,
            'kecamatan' => $this->district ? ucwords(strtolower($this->district->name)) : null,
            'kabupaten' => $this->regency ? ucwords(strtolower($this->regency->name)) : null,
            'provinsi' => $this->province ? ucwords(strtolower($this->province->name)) : null,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }
}
