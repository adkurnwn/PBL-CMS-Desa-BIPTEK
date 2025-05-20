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
        $danaDesa = DanaDesa::with(['images', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'danaDesa' => $danaDesa->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'images' => $item->images->map(function ($image) {
                        return asset('storage/' . $image->path);
                    }),
                    'created_at' => $item->created_at,
                ];
            }),
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
    public function show(DanaDesa $danaDesa)
    {
        //
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
