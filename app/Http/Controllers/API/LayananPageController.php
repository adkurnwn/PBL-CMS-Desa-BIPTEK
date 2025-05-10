<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ProdukDesa;
use Illuminate\Http\Request;

class LayananPageController extends Controller
{
    public function index()
    {
        $produkDesa = ProdukDesa::latest()->take(4)->get();

        return response()->json([
            'produkDesa' => $produkDesa->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'price' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                    'image' => [
                        'path' => $item->images->first()->path ?? null,
                    ],
                ];
            }),
        ]);
    }
}
