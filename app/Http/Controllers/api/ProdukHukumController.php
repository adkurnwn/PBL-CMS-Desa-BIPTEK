<?php

namespace App\Http\Controllers\api;

use App\Models\ProdukHukum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukHukum\ProdukHukumDetail;
use App\Http\Resources\ProdukHukum\ProdukHukumPreview;

class ProdukHukumController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $category = $request->query('category');
            $sort = $request->query('sort');

            $query = ProdukHukum::with(['category']);

            // Search by title or content
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('date', 'like', '%' . $search . '%');
                });
            }

            // Filter by category_id
            if ($category) {
                $query->where('category_id', $category);
            }

            // Sort by views or downloads
            if ($sort === 'views') {
                $query->orderBy('views', 'desc');
            } elseif ($sort === 'downloads') {
                $query->orderBy('downloads', 'desc');
            } else {
                $query->latest();
            }

            $produkHukum = $query->paginate(10);

            if ($produkHukum->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Produk Hukum found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => ProdukHukumPreview::collection($produkHukum),
                'meta' => [
                    'current_page' => $produkHukum->currentPage(),
                    'last_page' => $produkHukum->lastPage(),
                    'per_page' => $produkHukum->perPage(),
                    'total' => $produkHukum->total(),
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

    public function show($slug)
    {
        try {
            $produkHukum = ProdukHukum::with(['category', 'user:id,name'])->where('slug', $slug)->firstOrFail();

            if (!$produkHukum) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk Hukum not found'
                ], 404);
            }
            $produkHukum->increment('views');

            return response()->json([
                'success' => true,
                'data' => new ProdukHukumDetail($produkHukum),
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
