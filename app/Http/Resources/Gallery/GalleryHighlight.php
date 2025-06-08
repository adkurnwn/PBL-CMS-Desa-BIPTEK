<?php

namespace App\Http\Resources\Gallery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Gallery;

class GalleryHighlight extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = collect();
        $currentImages = $this->images;
        
        $images = $images->concat($currentImages);
        
        if ($images->count() < 5) {
            $needed = 5 - $images->count();
            
            $additionalImages = Gallery::where('id', '!=', $this->id)
                ->with('images')
                ->get()
                ->flatMap(function ($gallery) {
                    return $gallery->images;
                })
                ->take($needed);
                
            $images = $images->concat($additionalImages);
        }
        
        return [
            'images' => $images->take(5)->map(function ($image) {
                return [
                    'path' => $image->path,
                    'alt' => $image->alt
                ];
            }),
            'slug' => $this->slug,
        ];
    }
}
