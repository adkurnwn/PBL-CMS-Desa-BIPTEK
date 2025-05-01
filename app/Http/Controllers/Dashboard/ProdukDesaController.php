<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ProdukDesa;
use App\Helpers\SlugHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GambarProdukDesa;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validation\StoreProdukDesaRequest;
use App\Http\Requests\Validation\UpdateProdukDesaRequest;

class ProdukDesaController extends Controller
{
    protected $logHelper;
    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }
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

        $this->logHelper->logActivity('create', 'produk_desa', 'Menambahkan produk desa ' . $request->title);
        return redirect()->route('dashboard.produk-desa')->with('success', 'Produk Desa Berhasil Ditambahkan');
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

        $this->logHelper->logActivity('update', 'produk_desa', 'Memperbarui produk desa ' . $produkDesa->title);
        return redirect()->route('dashboard.produk-desa')->with('success', 'Produk Desa Berhasil Diperbarui');
    }

    public function destroy(ProdukDesa $produkDesa)
    {
        foreach ($produkDesa->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $produkDesa->delete();

        $this->logHelper->logActivity('delete', 'produk_desa', 'Menghapus produk desa ' . $produkDesa->title);
        return redirect()->route('dashboard.produk-desa')->with('success', 'Produk Desa Berhasil Dihapus');
    }
}
