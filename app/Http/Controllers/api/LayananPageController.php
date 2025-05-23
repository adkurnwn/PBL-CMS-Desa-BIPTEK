<?php

namespace App\Http\Controllers\api;

use App\Models\ProdukDesa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukDesaResource;

class LayananPageController extends Controller
{
    public function index()
    {
        $produkDesa = ProdukDesa::latest()->take(4)->get();

        return response()->json([
            'produkDesa' => ProdukDesaResource::collection($produkDesa),
        ]);
    }
}
