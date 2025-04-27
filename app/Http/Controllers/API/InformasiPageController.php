<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Event;
use App\Models\News;
use Illuminate\Http\Request;

class InformasiPageController extends Controller
{
    public function index()
    {
        $gallery = Gallery::latest()->first();
        $events = Event::latest()->take(3)->get();
        $news = News::latest()->take(6)->get();

        
    }
}
