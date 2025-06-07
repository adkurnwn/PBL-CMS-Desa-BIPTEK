<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\Gallery\GalleryPreview;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Gallery\GalleryDetail;
use App\Http\Resources\Gallery\GalleryHighlight;
use Illuminate\Support\Facades\Cache;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $category = $request->query('category');

            $query = Gallery::with(['images', 'category', 'user:id,name']);

            // Search by title or content
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%');
                });
            }

            if ($category) {
                $query->where('category_id', $category);
            }

            $galleries = $query->latest()->paginate(12);
            if ($galleries->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Galeri tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => GalleryPreview::collection($galleries),
                'meta' => [
                    'current_page' => $galleries->currentPage(),
                    'last_page' => $galleries->lastPage(),
                    'total' => $galleries->total(),
                    'per_page' => $galleries->perPage()
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

    public function highlight()
    {
        try {
            $gallery = Cache::remember('gallery_highlight', now()->addDay(), function () {
                return Gallery::with('images')->latest()->first();
            });
            if (!$gallery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gallery data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new GalleryHighlight($gallery)
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
    public function show($slug)
    {
        try {
            $gallery = Gallery::with(['images', 'category', 'user:id,name'])
                ->where('slug', $slug)
                ->first();
            if (!$gallery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gallery not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new GalleryDetail($gallery)
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
