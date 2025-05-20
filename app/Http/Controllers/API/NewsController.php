<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\Category;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::with(['images', 'category', 'user:id,name'])
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc')
            ->withCount('comments')
            ->paginate(12);

        return NewsResource::collection($news);
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
        $news = News::with(['images', 'category', 'user:id,name'])
            ->where('slug', $slug)
            ->where('is_draft', false)
            ->first();

        $category = Category::with(['news' => function ($query) {
            $query->where('is_draft', false)
                ->orderBy('created_at', 'desc')
                ->take(3);
        }])->find($news->category_id);

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
