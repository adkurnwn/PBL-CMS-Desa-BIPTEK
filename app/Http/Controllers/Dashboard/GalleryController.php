<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\News;
use Inertia\Inertia;
use App\Models\Gallery;
use App\Models\Category;
use App\Helpers\SlugHelper;
use App\Services\MediaService;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Validation\StoreGalleryRequest;
use App\Http\Requests\Validation\UpdateGalleryRequest;

class GalleryController extends Controller
{
    protected $mediaService;
    protected $categoryService;

    public function __construct(MediaService $mediaService, CategoryService $categoryService)
    {
        $this->mediaService = $mediaService;
        $this->categoryService = $categoryService;
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

        return redirect()->route('dashboard.galleries')->with('success', 'Gallery Added Successfully');
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

        // Update news record
        $gallery->update($data);

        // Handle image removals
        if ($request->removed_images) {
            $this->mediaService->handleImageRemovals($gallery, $request->removed_images);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($gallery, $request->file('images'), $request->alts);
        }

        return redirect()->route('dashboard.galleries')->with('success', 'Gallery Updated Successfully');
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
        return redirect()->route('dashboard.galleries.archive')->with('success', 'Gallery Restored Successfully');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->trashed()) {
            $this->mediaService->deleteAllMedia($gallery);
            $gallery->forceDelete();
            return redirect()->route('dashboard.galleries.archive')->with('success', 'Gallery Deleted Successfully');
        } else {
            $gallery->delete();
            return redirect()->route('dashboard.galleries')->with('success', 'Gallery Archived Successfully');
        }
    }
}
