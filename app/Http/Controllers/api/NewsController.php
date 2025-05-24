<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\News\NewsDetail;
use App\Http\Resources\News\NewsPreview;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $category = $request->query('category');
            $sort = $request->query('sort');

            $query = News::with(['images', 'category', 'user:id,name'])
                ->where('is_draft', false)
                ->withCount('comments');

            // Search by title or content
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%');
                });
            }

            // Filter by category_id
            if ($category) {
                $query->where('category_id', $category);
            }

            if ($sort === 'views') {
                $query->orderBy('views', 'desc');
            } else {
                $query->latest(); // Default sort by date
            }

            $news = $query->latest()->paginate(12);

            if ($news->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No news found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => NewsPreview::collection($news),
                'meta' => [
                    'current_page' => $news->currentPage(),
                    'last_page' => $news->lastPage(),
                    'per_page' => $news->perPage(),
                    'total' => $news->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }


    public function preview(Request $request)
    {
        $limit = $request->input('limit', 3);
        try {
            $news = News::with(['images', 'category', 'user:id,name'])
                ->where('is_draft', false)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();

            if ($news->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'News data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => NewsPreview::collection($news)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            $news = News::with(['images', 'category', 'user:id,name'])
                ->where('slug', $slug)
                ->where('is_draft', false)
                ->first();

            if (!$news) {
                return response()->json([
                    'success' => false,
                    'message' => 'News not found'
                ], 404);
            }

            $news->increment('views');

            return response()->json([
                'success' => true,
                'data' => new NewsDetail($news)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
