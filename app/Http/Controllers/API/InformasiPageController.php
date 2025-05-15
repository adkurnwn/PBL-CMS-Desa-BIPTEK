<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use App\Models\Event;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class InformasiPageController extends Controller
{
    public function index()
    {
        $gallery = Gallery::latest()->first();
        $events = Event::latest()->take(3)->get();
        $news = News::latest()->take(6)->get();

        return response()->json([
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
        ]);
    }
}
