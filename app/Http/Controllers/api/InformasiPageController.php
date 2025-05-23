<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use App\Models\Event;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\GalleryResource;

class InformasiPageController extends Controller
{
    public function index()
    {
        $gallery = Gallery::latest()->first();
        $events = Event::latest()->take(3)->get();
        $news = News::latest()->take(6)->get();

        return response()->json([
            'news' => NewsResource::collection($news),
            'events' => EventResource::collection($events),
            'gallery' => $gallery ? new GalleryResource($gallery) : null

        ]);
    }
}
