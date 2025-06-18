<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukDesa\ProdukDesaDetail;
use App\Http\Resources\ProdukDesa\ProdukDesaPreview;
use App\Http\Resources\ProdukDesaResource;
use App\Models\ProdukDesa;
use Illuminate\Http\Request;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Cache;

class ProdukDesaController extends Controller
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

            $query = ProdukDesa::with(['images']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            $produkDesa = $query->where('status', true)->latest()->paginate(12);
            if ($produkDesa->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk Desa tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => ProdukDesaPreview::collection($produkDesa),
                'meta' => [
                    'current_page' => $produkDesa->currentPage(),
                    'last_page' => $produkDesa->lastPage(),
                    'total' => $produkDesa->total(),
                    'per_page' => $produkDesa->perPage()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }

    public function preview()
    {
        try {
            $produkDesa = Cache::remember("produk_desa_preview", now()->addDay(), function () {
                return ProdukDesa::with(['images', 'user:id,name'])
                    ->where('status', true) // Only get active products
                    ->orderBy('created_at', 'desc')
                    ->take(4)
                    ->get();
            });

            if ($produkDesa->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk Desa data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => ProdukDesaPreview::collection($produkDesa)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug, Request $request)
    {
        try {
            $produkDesa = ProdukDesa::with(['images', 'user:id,name'])
                ->where('slug', $slug)
                ->where('status', true)
                ->first();

            if (!$produkDesa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk Desa data not found'
                ], 404);
            }
            $this->statisticsService->trackContentView('produk_desa', $produkDesa->id, $request);

            return response()->json([
                'success' => true,
                'data' => new ProdukDesaDetail($produkDesa)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }
}
