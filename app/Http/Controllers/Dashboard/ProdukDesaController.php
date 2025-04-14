<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ProdukDesa;
use Illuminate\Http\Request;
use App\Models\GambarProdukDesa;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukDesaController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/ProdukDesa/Index', [
            'produkDesa' => ProdukDesa::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/ProdukDesa/Create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|decimal:0',
            'kontak_penjual' => 'required|string|max:20',
            'description' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $request->validate($rules);

        $dataProduk = $request->all();
        $dataProduk['user_id'] = Auth::id();

        try {
            $produkDesa = ProdukDesa::create($dataProduk);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('produk-desa-images', 'public');

                $produkDesa->images()->create([
                    'produk_desa_id' => $produkDesa->id,
                    'images' => $path,
                ]);
            }
        }
        return redirect()->route('dashboard.produk-desa')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(ProdukDesa $produk)
    {
        return Inertia::render('Dashboard/ProdukDesa/Edit', [
            'produk' => $produk
        ]);
    }

    public function update(Request $request, ProdukDesa $produkDesa)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|decimal|min:0',
            'kontak_penjual' => 'required|string|max:20',
            'description' => 'required|text',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:gambar_produk_desa,id',
        ];

        $request->validate($rules);

        $produkDesa->update($request->all());

        if ($request->removed_images) {
            foreach ($request->removed_images as $imageId) {
                $image = GambarProdukDesa::find($imageId);
                if ($image && $image->produk_desa_id === $produkDesa->id) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('produk-desa-images', 'public');

                $produkDesa->images()->create([
                    'produk_desa_id' => $produkDesa->id,
                    'images' => $path,
                ]);
            }
        }

        return redirect()->route('dashboard.produk_desa.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(ProdukDesa $produkDesa)
    {
        $produkDesa->delete();
        return redirect()->route('dashboard.produk_desa.index')->with('success', 'Produk berhasil dihapus.');
    }
}
