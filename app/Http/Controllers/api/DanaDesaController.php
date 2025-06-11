<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DanaDesa\DanaDesaDetail;
use App\Http\Resources\DanaDesa\DanaDesaPreview;
use App\Models\DanaDesa;
use Illuminate\Http\Request;
use App\Services\StatisticsService;

class DanaDesaController extends Controller
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
            $sort = $request->query('sort');
            $year = $request->query('year');

            $query = DanaDesa::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('date', 'like', '%' . $search . '%');
                });
            }

            // Filter by year
            if ($year) {
                $query->whereYear('date', $year);
            }

            if ($sort === 'views') {
                $query->orderBy('views', 'desc');
            } elseif ($sort === 'downloads') {
                $query->orderBy('downloads', 'desc');
            } else {
                $query->latest();
            }

            $danaDesa = $query->latest()->paginate(10);

            if ($danaDesa->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dana Desa tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => DanaDesaPreview::collection($danaDesa),
                'meta' => [
                    'current_page' => $danaDesa->currentPage(),
                    'last_page' => $danaDesa->lastPage(),
                    'per_page' => $danaDesa->perPage(),
                    'total' => $danaDesa->total(),
                ]
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
            $danaDesa = DanaDesa::where('slug', $slug)->first();

            if (!$danaDesa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dana Desa not found'
                ], 404);
            }
            $this->statisticsService->trackContentView('dana_desa', $danaDesa->id, $request);
            $danaDesa->increment('views');

            return response()->json([
                'success' => true,
                'data' => new DanaDesaDetail($danaDesa)
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
     * Handle file download and increment download count.
     */
    public function download($slug, Request $request)
    {
        try {
            $danaDesa = DanaDesa::where('slug', $slug)->first();

            if (!$danaDesa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dana Desa not found'
                ], 404);
            }

            if (!$danaDesa->file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not available'
                ], 404);
            }

            // Increment download count
            $danaDesa->increment('downloads');

            // Return file path for download
            return response()->json([
                'success' => true,
                'file_url' => asset('storage/' . $danaDesa->file)
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
