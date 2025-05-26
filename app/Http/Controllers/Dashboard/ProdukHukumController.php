<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Category;
use App\Helpers\SlugHelper;
use App\Models\ProdukHukum;
use App\Services\CategoryService;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Validation\StoreProdukHukumRequest;
use App\Http\Requests\Validation\UpdateProdukHukumRequest;
use App\Services\MediaService;

class ProdukHukumController extends Controller
{
    protected $mediaService;
    protected $categoryService;
    protected $logHelper;

    public function __construct(CategoryService $categoryService, MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        $this->categoryService = $categoryService;
        $this->logHelper = new LogActivityHelper();
    }

    public function index(Request $request)
    {
        // Get query parameters
        $search = $request->input('search');
        $filters = $request->except(['search', 'sort', 'direction', 'page', '_token']);
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $perPage = 10;

        // Start building the query
        $query = ProdukHukum::with('category');

        // Apply search
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Apply filters
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($key === 'category_id' && !empty($value)) {
                    $query->where('category_id', $value);
                }
                
                if ($key === 'year' && !empty($value)) {
                    // Use the date column instead of created_at
                    $query->whereYear('date', $value);
                }
            }
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $produkHukum = $query->paginate($perPage)->withQueryString();

        // Get available years for the filter - using date column
        $availableYears = ProdukHukum::select(DB::raw('YEAR(date) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(function($year) {
                return ['id' => $year, 'name' => $year];
            })
            ->toArray();

        return inertia('Dashboard/ProdukHukum/Index', [
            'produkHukum' => $produkHukum,
            'categories' => Category::where('category_type', 0)->get(),
            'availableYears' => $availableYears,
            'search' => $search,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/ProdukHukum/Create', [
            'categories' => Category::where('category_type', 0)->get()
        ]);
    }

    public function store(StoreProdukHukumRequest $request)
    {
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? [], 0);

        $data = $request->all();
        $data['slug'] = SlugHelper::generateUniqueSlug(ProdukHukum::class, $request->title);
        $data['user_id'] = Auth::id();
        $data['category_id'] = $category_id;

        if ($request->hasFile('file')) {
            $data['file'] = $this->mediaService->handleDocumentUpload(
                $request->file('file'),
                $data['slug'],
                'produk-hukum-documents'
            );
        }

        ProdukHukum::create($data);

        $this->logHelper->logActivity('create', 'produk_hukum', 'Menambahkan produk hukum ' . $request->title);
        return redirect()->route('dashboard.produk-hukum.index')->with('success', 'Produk Hukum Berhasil Ditambahkan');
    }

    public function edit($slug)
    {
        $produkHukum = ProdukHukum::where('slug', $slug)->firstOrFail();
        $categories = $this->categoryService->getCategoriesWithUsageCounts(0);

        return Inertia::render('Dashboard/ProdukHukum/Edit', [
            'produkHukum' => $produkHukum->load('category'),
            'categories' => $categories,
            'unused_categories' => $this->categoryService->getUnusedCategories(0)
        ]);
    }

    public function update(UpdateProdukHukumRequest $request, $slug)
    {
        $produkHukum = ProdukHukum::where('slug', $slug)->firstOrFail();
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? [], 0);

        if (!empty($request->category['deleting_category_id'])) {
            $deletionResults = $this->categoryService->handleCategoryDeletion($request->category['deleting_category_id'], 0);
            session()->flash('categoryDeletionResults', $deletionResults);
        }

        $data = $request->all();

        if ($request->hasFile('file')) {
            $data['file'] = $this->mediaService->handleDocumentUpload(
                $request->file('file'),
                $request->title,
                'produk-hukum-documents',
                $produkHukum->file
            );

            // Delete the old file if it exists
            if ($produkHukum->file) {
                Storage::disk('public')->delete($produkHukum->file);
            }

        } else {
            unset($data['file']);
        }

        if ($produkHukum->title !== $request->title) {
            $data['slug'] = SlugHelper::generateUniqueSlug(ProdukHukum::class, $request->title);
        }
        $data['category_id'] = $category_id;

        $produkHukum->update($data);

        $this->logHelper->logActivity('update', 'produk_hukum', 'Memperbarui produk hukum ' . $produkHukum->title);
        return redirect()->route('dashboard.produk-hukum.index')->with('success', 'Produk Hukum Berhasil Diperbarui');
    }
}
