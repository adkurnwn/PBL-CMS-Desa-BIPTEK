<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use App\Models\News;
use App\Models\Event;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\ProdukDesa;

class HomePageController extends Controller
{
    public function index()
    {
        $profile = Profile::first();
        $news = News::with(['images', 'category'])->where('is_draft', false)->latest()->take(3)->get();
        $events = Event::latest()->take(3)->get();
        $gallery = Gallery::with('images')->latest()->first();
        $produkDesa = ProdukDesa::with('images')->latest()->take(4)->get();

        return response()->json([
            'name' => $profile->name,
            'hero_image' => json_decode($profile->hero_images)[0],
            'news' => $news->map(function ($item) {
                return [
                    'slug' => $item->slug,
                    'title' => $item->title,
                    'content' => $item->content,
                    'created_at' => Carbon::parse($item->created_at)->format('j F Y'),
                    'views' => $item->views,
                    'image' => [
                        'path' => $item->images->first()->path ?? null,
                        'alt' => $item->images->first()->alt ?? null
                    ],
                    'category' => $item->category,
                    'author' => [
                        'name' => $item->user->name,
                    ],
                ];
            }),
            'events' => $events->map(function ($item) {
                return [
                    'title' => $item->title,
                    'date' => Carbon::parse($item->date)->format('D, j M Y'),
                    'time' => Carbon::parse($item->time)->format('H:i'),
                    'location' => $item->location,
                ];
            }),
            'gallery' => [
                'images' => $gallery->images->take(5)->map(function ($image) {
                    return [
                        'path' => $image->path,
                        'alt' => $image->alt
                    ];
                }),
            ],
            'produkDesa' => $produkDesa->map(function ($item) {
                return [
                    'name' => $item->name,
                    'price' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                    'image' => [
                        'path' => $item->images->first()->path ?? null,
                    ],
                ];
            }),
        ]);
    }
}
