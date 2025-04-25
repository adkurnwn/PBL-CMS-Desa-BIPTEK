<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::with(['images', 'category'])
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($news);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $news = News::with(['images', 'category'])
            ->where('slug', $slug)
            ->where('is_draft', false)
            ->first();

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        // Increment view count
        $news->increment('views');

        return response()->json($news);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
