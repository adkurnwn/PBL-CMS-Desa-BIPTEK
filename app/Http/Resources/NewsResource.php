<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\KomentarBerita;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => Carbon::parse($this->created_at)->format('j F Y'),
            'views' => $this->views,
            'comments_count' => KomentarBerita::where('id_berita', $this->id)
                ->where('is_approved', true)
                ->count(),
            'image' => [
                'path' => $this->images->first()->path ?? null,
                'alt' => $this->images->first()->alt ?? null
            ],
            'category' => $this->category,
            'author' => [
                'name' => $this->user->name,
            ],
        ];
    }
}
