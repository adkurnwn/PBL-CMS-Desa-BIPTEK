<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Gallery;
use App\Models\Category;
use App\Helpers\SlugHelper;
use App\Services\MediaService;
use App\Services\CategoryService;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Validation\StoreGalleryRequest;
use App\Http\Requests\Validation\UpdateGalleryRequest;

class GalleryController extends Controller
{
    protected $mediaService;
    protected $categoryService;
    protected $logHelper;

    public function __construct(MediaService $mediaService, CategoryService $categoryService)
    {
        $this->mediaService = $mediaService;
        $this->categoryService = $categoryService;
        $this->logHelper = new LogActivityHelper();
    }

    public function index(Request $request)
    {
        // Get request parameters for search and sorting
        $search = $request->input('search');
        $sortField = $request->input('sort', 'title');
        $sortDirection = $request->input('direction', 'asc');
        
        // Get filter parameters
        $filters = [];
        if ($request->has('category_id')) {
            $filters['category_id'] = $request->input('category_id');
        }
        
        // Start building the query
        $query = Gallery::with(['category', 'images']);
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply category filter if provided
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        // Handle sorting
        if ($sortField === 'category.name') {
            $query->join('categories', 'galleries.category_id', '=', 'categories.id')
                ->select('galleries.*')
                ->orderBy('categories.name', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }
        
        // Paginate the results
        $galleries = $query->paginate(25)->withQueryString();
        
        return Inertia::render('Dashboard/Gallery/Index', [
            'galleries' => $galleries,
            'search' => $search,
            'sort' => $sortField,
            'direction' => $sortDirection,
            'filters' => $filters,
            'categories' => Category::where('category_type', 1)->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Gallery/Create', [
            'categories' => Category::where('category_type', 1)->get(),
        ]);
    }

    public function store(StoreGalleryRequest $request)
    {
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? []);

        $data = $request->all();
        $data['slug'] = SlugHelper::generateUniqueSlug(Gallery::class, $request->title);
        $data['user_id'] = Auth::id();
        $data['category_id'] = $category_id;

        $gallery = Gallery::create($data);

        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($gallery, $request->file('images'), $request->alts);
        }

        $this->logHelper->logActivity('create', 'gallery', 'Menambahkan gallery ' . $gallery->title);
        return redirect()->route('dashboard.galleries.index')->with('success', 'Gallery Berhasil Ditambahkan');
    }

    public function edit($slug)
    {
        $gallery = Gallery::where('slug', $slug)->firstOrFail();
        $categories = $this->categoryService->getCategoriesWithUsageCounts(1);
        return Inertia::render('Dashboard/Gallery/Edit', [
            'gallery' => $gallery->load('images', 'category'),
            'categories' => Category::where('category_type', 1)->get(),
            'unused_categories' => $this->categoryService->getUnusedCategories(1),
        ]);
    }

    public function update(UpdateGalleryRequest $request, $slug)
    {
        $gallery = Gallery::where('slug', $slug)->firstOrFail();
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? []);

        if (!empty($request->category['deleting_category_id'])) {
            $deletionResults = $this->categoryService->handleCategoryDeletion($request->category['deleting_category_id']);
            session()->flash('categoryDeletionResults', $deletionResults);
        }

        $data = $request->all();
        if ($gallery->title !== $request->title) {
            $data['slug'] = SlugHelper::generateUniqueSlug(Gallery::class, $request->title);
        }
        $data['category_id'] = $category_id;

        $gallery->update($data);

        if ($request->removed_images) {
            $this->mediaService->handleImageRemovals($gallery, $request->removed_images);
        }

        if ($request->updated_existing_images) {
            $this->mediaService->handleImageAltTextUpdates($gallery, $request->updated_existing_images);
        }

        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($gallery, $request->file('images'), $request->alts);
        }

        $this->logHelper->logActivity('edit', 'gallery', 'Memperbarui gallery ' . $gallery->title);
        return redirect()->route('dashboard.galleries.index')->with('success', 'Gallery Berhasil Diperbarui');
    }

    public function archive(Request $request)
    {
        // Get request parameters for search and sorting
        $search = $request->input('search');
        $sortField = $request->input('sort', 'deleted_at');
        $sortDirection = $request->input('direction', 'desc');
        
        // Get filter parameters
        $filters = [];
        if ($request->has('category_id')) {
            $filters['category_id'] = $request->input('category_id');
        }
        
        // Start building the query
        $query = Gallery::onlyTrashed()->with(['category', 'images']);
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply category filter if provided
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        // Handle sorting
        if ($sortField === 'category.name') {
            $query->join('categories', 'galleries.category_id', '=', 'categories.id')
                ->select('galleries.*')
                ->orderBy('categories.name', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }
        
        // Paginate the results
        $galleries = $query->paginate(25)->withQueryString();
        
        return Inertia::render('Dashboard/Gallery/Archive', [
            'galleries' => $galleries,
            'search' => $search,
            'sort' => $sortField,
            'direction' => $sortDirection,
            'filters' => $filters,
            'categories' => Category::where('category_type', 1)->get()
        ]);
    }

    public function restore($slug)
    {
        $gallery = Gallery::withTrashed()->where('slug', $slug)->firstOrFail();
        $gallery->restore();
        $this->logHelper->logActivity('restore', 'gallery', 'Memulihkan gallery ' . $gallery->title);
        return redirect()->route('dashboard.galleries.archive')->with('success', 'Gallery Berhasil Dipulihkan');
    }

    public function destroy($slug)
    {
        $gallery = Gallery::withTrashed()->where('slug', $slug)->firstOrFail();
        if ($gallery->trashed()) {
            $this->mediaService->deleteAllMedia($gallery);
            $gallery->forceDelete();
            $this->logHelper->logActivity('delete', 'gallery', 'Menghapus gallery ' . $gallery->title);
            return redirect()->route('dashboard.galleries.archive')->with('success', 'Gallery Berhasil Dihapus');
        } else {
            $gallery->delete();
            $this->logHelper->logActivity('archive', 'gallery', 'Mengarsipkan gallery ' . $gallery->title);
            return redirect()->route('dashboard.galleries.index')->with('success', 'Gallery Berhasil Diarsipkan');
        }
    }
}
