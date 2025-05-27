<?php

namespace App\Http\Resources\News;

use Carbon\Carbon;
use App\Models\KomentarBerita;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsPreview extends JsonResource
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
            'comments_count' => $this->comments->where('is_approved', true)->count(),
            'thumbnail' =>  $this->images->first()->path ?? null,
            'alt' => $this->images->first()->alt ?? null,
            'category_name' => $this->category->name,
            'author' =>  $this->user->name,
        ];
    }
}
