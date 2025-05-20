<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DanaDesa;
use Illuminate\Http\Request;

class DanaDesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $danaDesa = DanaDesa::orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'data' => $danaDesa->map(function ($item) {
                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'title' => $item->title,
                    'views' => $item->views,
                    'downloads' => $item->downloads,
                    'date' => $item->date,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'pagination' => [
                'total' => $danaDesa->total(),
                'per_page' => $danaDesa->perPage(),
                'current_page' => $danaDesa->currentPage(),
                'last_page' => $danaDesa->lastPage(),
                'from' => $danaDesa->firstItem(),
                'to' => $danaDesa->lastItem(),
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
        $danaDesa = DanaDesa::where('slug', $slug)->first();

        if (!$danaDesa) {
            return response()->json(['message' => 'Dana Desa not found'], 404);
        }

        // Increment view count
        $danaDesa->increment('views');

        return response()->json($danaDesa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DanaDesa $danaDesa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DanaDesa $danaDesa)
    {
        //
    }
}
