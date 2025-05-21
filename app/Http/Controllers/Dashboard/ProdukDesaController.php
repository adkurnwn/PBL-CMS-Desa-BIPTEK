<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ProdukDesa;
use App\Helpers\SlugHelper;
use App\Services\MediaService;
use App\Helpers\LogActivityHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validation\StoreProdukDesaRequest;
use App\Http\Requests\Validation\UpdateProdukDesaRequest;

class ProdukDesaController extends Controller
{
    protected $logHelper;
    protected $mediaService;
    public function __construct(MediaService $mediaService)
    {
        $this->logHelper = new LogActivityHelper();
        $this->mediaService = new MediaService();
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
            $this->mediaService->handleImageUploads($produkDesa, $request->file('images'), [], 'produk-desa-images');
        }

        $this->logHelper->logActivity('create', 'produk_desa', 'Menambahkan produk desa ' . $request->title);
        return redirect()->route('dashboard.produk-desa.index')->with('success', 'Produk Desa Berhasil Ditambahkan');
    }

    public function edit($slug)
    {
        $produkDesa = ProdukDesa::where('slug', $slug)->firstOrFail();

        $this->logHelper->logActivity('view', 'produk_desa', 'Melihat produk desa ' . $produkDesa->title);
        return Inertia::render('Dashboard/ProdukDesa/Edit', [
            'produkDesa' => $produkDesa->load('images'),

        ]);
    }

    public function update(UpdateProdukDesaRequest $request, $slug)
    {
        $produkDesa = ProdukDesa::where('slug', $slug)->firstOrFail();

        $dataProduk = $request->all();
        $dataProduk['slug'] = SlugHelper::generateUniqueSlug(ProdukDesa::class, $request->product_name);

        $produkDesa->update($dataProduk);

        if ($request->removed_images) {
            $this->mediaService->handleImageRemovals($produkDesa, $request->removed_images, 'produk_desa_id');
        }

        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($produkDesa, $request->file('images'), [], 'produk-desa-images');
        }

        $this->logHelper->logActivity('update', 'produk_desa', 'Memperbarui produk desa ' . $produkDesa->product_name);
        return redirect()->route('dashboard.produk-desa.index')->with('success', 'Produk Desa Berhasil Diperbarui');
    }

    public function destroy($slug)
    {
        $produkDesa = ProdukDesa::where('slug', $slug)->firstOrFail();

        $newStatus = !(bool)$produkDesa->status;
        $produkDesa->update([
            'status' => $newStatus
        ]);

        $logMessage = $newStatus ? 'Mengaktifkan' : 'Menonaktifkan';
        $statusMessage = $newStatus ? 'Diaktifkan' : 'Dinonaktifkan';
        $logStatus = $newStatus ? 'restore' : 'delete';
        $this->logHelper->logActivity($logStatus, 'produk_desa', $logMessage . ' produk desa ' . $produkDesa->product_name);
        return redirect()->route('dashboard.produk-desa.index')->with('success', 'Produk Desa Berhasil ' . $statusMessage);
    }
}
