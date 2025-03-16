<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ProdukHukum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProdukHukumController extends Controller
{
    public function index()
    {
        return inertia('Dashboard/ProdukHukum/Index', [
            'produkHukum' => ProdukHukum::all()
        ]);
    }

    public function create()
    {
        return inertia('Dashboard/ProdukHukum/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'file' => 'required',
            'date' => 'required',
        ]);

        ProdukHukum::create([
            'title' => $request->title,
            'description' => $request->description,
            'file' => $request->file,
            'date' => $request->date,
        ]);

        return redirect()->route('dashboard.produk-hukum');
    }
}
