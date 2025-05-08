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

    public function index()
    {
        return inertia('Dashboard/ProdukHukum/Index', [
            'produkHukum' => ProdukHukum::with('category')->get()
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
            $path = $request->file('file')->store('produk-hukum-documents', 'public');
            $data['file'] = $path;
        }

        ProdukHukum::create($data);

        $this->logHelper->logActivity('create', 'produk_hukum', 'Menambahkan produk hukum ' . $request->title);
        return redirect()->route('dashboard.produk-hukum.index')->with('success', 'Produk Hukum Berhasil Ditambahkan');
    }

    public function edit(ProdukHukum $produkHukum)
    {
        $categories = $this->categoryService->getCategoriesWithUsageCounts(0);

        return Inertia::render('Dashboard/ProdukHukum/Edit', [
            'produkHukum' => $produkHukum->load('category'),
            'categories' => $categories,
            'unused_categories' => $this->categoryService->getUnusedCategories(0)
        ]);
    }

    public function update(UpdateProdukHukumRequest $request, ProdukHukum $produkHukum)
    {
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? [], 0);

        if (!empty($request->category['deleting_category_id'])) {
            $deletionResults = $this->categoryService->handleCategoryDeletion($request->category['deleting_category_id'], 0);
            session()->flash('categoryDeletionResults', $deletionResults);
        }

        $data = $request->all();

        if ($request->hasFile('file')) {
            if ($produkHukum->file) {
                Storage::disk('public')->delete($produkHukum->file);
            }
            $path = $request->file('file')->store('produk-hukum-documents', 'public');
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
