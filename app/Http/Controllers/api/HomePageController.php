<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Profile;
use App\Models\ProdukDesa;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\ProdukDesaResource;

class HomePageController extends Controller
{
    public function index()
    {
        $profile = Profile::first();
        $news = News::with(['images', 'category', 'user'])
            ->where('is_draft', false)
            ->latest()
            ->take(3)
            ->get();

        $events = Event::latest()->take(3)->get();
        $gallery = Gallery::with('images')->latest()->first();
        $produkDesa = ProdukDesa::with('images')->latest()->take(4)->get();

        return response()->json([
            'name' => $profile->name,
            'hero_image' => json_decode($profile->hero_images)[0],
            'news' => NewsResource::collection($news),
            'events' => EventResource::collection($events),
            'gallery' => $gallery ? new GalleryResource($gallery) : null,
            'produkDesa' => ProdukDesaResource::collection($produkDesa),
        ]);
    }
}
