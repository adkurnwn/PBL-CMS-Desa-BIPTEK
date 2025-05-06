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

    public function index()
    {
        return Inertia::render('Dashboard/Gallery/Index', [
            'galleries' => Gallery::all(),
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

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($gallery, $request->file('images'), $request->alts);
        }

        $this->logHelper->logActivity('create', 'gallery', 'Menambahkan gallery ' . $gallery->title);
        return redirect()->route('dashboard.galleries.index')->with('success', 'Gallery Berhasil Ditambahkan');
    }

    public function edit(Gallery $gallery)
    {
        $categories = $this->categoryService->getCategoriesWithUsageCounts(1);
        return Inertia::render('Dashboard/Gallery/Edit', [
            'gallery' => $gallery->load('images', 'category'),
            'categories' => Category::where('category_type', 1)->get(),
            'unused_categories' => $this->categoryService->getUnusedCategories(1),
        ]);
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery)
    {
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? []);

        if (!empty($request->category['deleting_category_id'])) {
            $deletionResults = $this->categoryService->handleCategoryDeletion($request->category['deleting_category_id']);
            session()->flash('categoryDeletionResults', $deletionResults);
        }

        $data = $request->all();
        $data['slug'] = SlugHelper::generateUniqueSlug(Gallery::class, $request->title);
        $data['category_id'] = $category_id;

        $gallery->update($data);

        // Handle image removals
        if ($request->removed_images) {
            $this->mediaService->handleImageRemovals($gallery, $request->removed_images);
        }

        // Handle updated alt texts for existing images
        if ($request->updated_existing_images) {
            $this->mediaService->handleImageAltTextUpdates($gallery, $request->updated_existing_images);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($gallery, $request->file('images'), $request->alts);
        }

        $this->logHelper->logActivity('edit', 'gallery', 'Memperbarui gallery ' . $gallery->title);
        return redirect()->route('dashboard.galleries.index')->with('success', 'Gallery Berhasil Diperbarui');
    }

    public function archive(Gallery $gallery)
    {
        Gallery::onlyTrashed()->with('category')->get();
        return Inertia::render('Dashboard/Gallery/Archive', [
            'galleries' => Gallery::onlyTrashed()->with('category')->get(),
        ]);
    }

    public function restore(Gallery $gallery)
    {
        $gallery->restore();
        $this->logHelper->logActivity('restore', 'gallery', 'Memulihkan gallery ' . $gallery->title);
        return redirect()->route('dashboard.galleries.archive')->with('success', 'Gallery Berhasil Dipulihkan');
    }

    public function destroy(Gallery $gallery)
    {
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
