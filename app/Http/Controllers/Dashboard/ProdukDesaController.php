<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ProdukDesa;
use App\Helpers\SlugHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GambarProdukDesa;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validation\StoreProdukDesaRequest;
use App\Http\Requests\Validation\UpdateProdukDesaRequest;
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

    public function store(StoreProdukDesaRequest $request)
    {
        $dataProduk = $request->all();
        $dataProduk['slug'] = SlugHelper::generateUniqueSlug(ProdukDesa::class, $request->title);
        $dataProduk['user_id'] = Auth::id();

        $produkDesa = ProdukDesa::create($dataProduk);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('produk-desa-images', 'public');

                $produkDesa->images()->create([
                    'produk_desa_id' => $produkDesa->id,
                    'path' => $path,
                ]);
            }
        }
        return redirect()->route('dashboard.produk-desa')->with('success', 'Product Created Successfully');
    }

    public function edit(ProdukDesa $produkDesa)
    {
        return Inertia::render('Dashboard/ProdukDesa/Edit', [
            'produkDesa' => $produkDesa->load('images'),

        ]);
    }

    public function update(UpdateProdukDesaRequest $request, ProdukDesa $produkDesa)
    {
        $dataProduk = $request->all();
        $dataProduk['slug'] = SlugHelper::generateUniqueSlug(ProdukDesa::class, $request->title);

        $produkDesa->update($dataProduk);

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
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('dashboard.produk-desa')->with('success', 'Product Updated Successfully');
    }

    public function destroy(ProdukDesa $produkDesa)
    {
        foreach ($produkDesa->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $produkDesa->delete();
        return redirect()->route('dashboard.produk-desa')->with('success', 'Product Deleted Successfully');
    }
}
