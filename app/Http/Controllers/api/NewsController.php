<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Services\StatisticsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\News\NewsDetail;
use App\Http\Resources\News\NewsPreview;

class NewsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $category = $request->query('category');
            $sort = $request->query('sort');
            $timeFilter = $request->query('time_filter');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

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

            if ($category) {
                $query->where('category_id', $category);
            }

            // Add time filtering
            if ($timeFilter) {
                switch ($timeFilter) {
                    case 'hari_ini':
                        $query->whereDate('created_at', today());
                        break;
                    case 'kemarin':
                        $query->whereDate('created_at', today()->subDay());
                        break;
                    case 'sebulan':
                        $query->where('created_at', '>=', today()->subMonth());
                        break;
                    case 'custom':
                        if ($startDate && $endDate) {
                            $query->whereBetween('created_at', [
                                $startDate . ' 00:00:00',
                                $endDate . ' 23:59:59'
                            ]);
                        } elseif ($startDate) {
                            $query->where('created_at', '>=', $startDate . ' 00:00:00');
                        } elseif ($endDate) {
                            $query->where('created_at', '<=', $endDate . ' 23:59:59');
                        }
                        break;
                }
            }

            if ($sort === 'views') {
                $query->orderBy('views', 'desc');
            } else {
                $query->latest();
            }

            $news = $query->latest()->paginate(12);

            if ($news->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Berita tidak ditemukan'
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
        $sort = $request->input('sort', 'created_at');

        try {
            // Create cache key that includes sort and limit parameters
            $cacheKey = "news_preview_{$sort}_{$limit}";

            $news = Cache::remember($cacheKey, now()->addDay(), function () use ($sort, $limit) {
                $query = News::with(['images', 'category', 'user:id,name'])
                    ->where('is_draft', false);

                if ($sort === 'views') {
                    $query->orderBy('views', 'desc');
                } else {
                    $query->latest();
                }

                // Return the actual data, not the query builder
                return $query->take($limit)->get();
            });

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
    public function show($slug, Request $request)
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
            $this->statisticsService->trackContentView('news', $news->id, $request);

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
