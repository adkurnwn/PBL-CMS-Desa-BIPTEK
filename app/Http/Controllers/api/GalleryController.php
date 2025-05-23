<?php

namespace App\Http\Controllers\api;

use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gallery = Gallery::with(['images', 'category', 'user:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'data' => $gallery->map(function ($item) {
                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'title' => $item->title,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'pagination' => [
                'total' => $gallery->total(),
                'per_page' => $gallery->perPage(),
                'current_page' => $gallery->currentPage(),
                'last_page' => $gallery->lastPage(),
                'from' => $gallery->firstItem(),
                'to' => $gallery->lastItem(),
            ]
        ]);
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
        $gallery = Gallery::with(['images', 'category', 'user:id,name'])
            ->where('slug', $slug)
            ->first();

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        return response()->json($gallery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        //
    }
}
