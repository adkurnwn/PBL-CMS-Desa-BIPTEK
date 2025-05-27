<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ProdukDesa;
use App\Helpers\SlugHelper;
use App\Services\MediaService;
use App\Helpers\LogActivityHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    
    public function index(Request $request)
    {
        // Get search parameters
        $search = $request->input('search');
        $filters = $request->input('filters', []);
        $sort = $request->input('sort', 'updated_at');
        $direction = $request->input('direction', 'desc');
        $perPage = $request->input('per_page', 25);

        // Query builder for product filtering
        $query = ProdukDesa::query()->with('images');

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('nama_penjual', 'like', "%{$search}%")
                  ->orWhere('kontak_penjual', 'like', "%{$search}%")
                  ->orWhere('alamat_penjual', 'like', "%{$search}%");
            });
        }

        // Apply filters - Fix to properly handle status filter
        if (isset($filters['status'])) {
            // Convert string '0' or '1' to integer/boolean for proper comparison
            $statusValue = filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            // If we got a valid boolean or null, convert to integer for DB query
            if ($statusValue !== null) {
                $query->where('status', (int)$statusValue);
            } elseif (is_numeric($filters['status'])) {
                // Direct numeric comparison if it's a valid number
                $query->where('status', (int)$filters['status']);
            }
        }

        // Apply sorting
        if (in_array($sort, ['product_name', 'price', 'nama_penjual', 'kontak_penjual', 'created_at', 'updated_at', 'status'])) {
            $query->orderBy($sort, $direction);
        }

        // Paginate results
        $produkDesa = $query->paginate($perPage)->withQueryString();

        // Log activity
        $this->logHelper->logActivity('view', 'produk_desa', 'Melihat daftar produk desa');

        return Inertia::render('Dashboard/ProdukDesa/Index', [
            'produkDesa' => $produkDesa,
            'search' => $search,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction
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
