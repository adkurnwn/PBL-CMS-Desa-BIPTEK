<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukDesaResource;
use App\Models\ProdukDesa;
use Illuminate\Http\Request;

class ProdukDesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produkDesa = ProdukDesa::orderBy('created_at', 'desc')
            ->paginate(12);

        return ProdukDesaResource::collection($produkDesa);
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
        $produkDesa = ProdukDesa::where('slug', $slug)->first();

        if (!$produkDesa) {
            return response()->json(['message' => 'Produk Desa not found'], 404);
        }

        return response()->json($produkDesa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProdukDesa $produkDesa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProdukDesa $produkDesa)
    {
        //
    }
}
