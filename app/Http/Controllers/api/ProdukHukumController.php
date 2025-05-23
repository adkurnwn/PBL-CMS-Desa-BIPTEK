<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ProdukHukum;
use Illuminate\Http\Request;

class ProdukHukumController extends Controller
{
    public function index()
    {
        $produkHukum = ProdukHukum::with('category')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return response()->json($produkHukum);
    }

    public function show($id)
    {
        $produkHukum = ProdukHukum::with('category')->findOrFail($id);
        return response()->json($produkHukum);
    }
}
