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

class ProdukHukumController extends Controller
{
    protected $categoryService;
    protected $logHelper;

    public function __construct(CategoryService $categoryService)
    {
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
            // Generate a sanitized filename from the title with timestamp
            $sanitizedTitle = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $request->title);
            $sanitizedTitle = preg_replace('/_+/', '_', $sanitizedTitle); // Remove multiple underscores
            $sanitizedTitle = trim($sanitizedTitle, '_'); // Remove leading/trailing underscores
            
            $timestamp = now()->format('Y-m-d_H-i-s');
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = $sanitizedTitle . '_' . $timestamp . '.' . $extension;

            // Store the file with the custom filename
            $path = $request->file('file')->storeAs('produk-hukum-documents', $filename, 'public');
            $data['file'] = $path;
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
            // Delete the old file if it exists
            if ($produkHukum->file) {
                Storage::disk('public')->delete($produkHukum->file);
            }

            // Generate a sanitized filename from the title with timestamp
            $sanitizedTitle = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $request->title);
            $sanitizedTitle = preg_replace('/_+/', '_', $sanitizedTitle); // Remove multiple underscores
            $sanitizedTitle = trim($sanitizedTitle, '_'); // Remove leading/trailing underscores
            
            $timestamp = now()->format('Y-m-d_H-i-s');
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = $sanitizedTitle . '_' . $timestamp . '.' . $extension;

            // Store the file with the custom filename
            $path = $request->file('file')->storeAs('produk-hukum-documents', $filename, 'public');
            $data['file'] = $path;
        } else {
            unset($data['file']);
        }

        $data['slug'] = SlugHelper::generateUniqueSlug(ProdukHukum::class, $request->title);
        $data['category_id'] = $category_id;

        $produkHukum->update($data);

        $this->logHelper->logActivity('update', 'produk_hukum', 'Memperbarui produk hukum ' . $produkHukum->title);
        return redirect()->route('dashboard.produk-hukum.index')->with('success', 'Produk Hukum Berhasil Diperbarui');
    }
}
