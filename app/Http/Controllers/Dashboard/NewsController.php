<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\News;
use Inertia\Inertia;
use App\Models\Category;
use App\Helpers\SlugHelper;
use App\Services\MediaService;
use App\Services\CategoryService;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Validation\StoreNewsRequest;
use App\Http\Requests\Validation\UpdateNewsRequest;

class NewsController extends Controller
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
        return Inertia::render('Dashboard/News/Index', [
            'news' => News::with('category')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/News/Create', [
            'categories' => Category::where('category_type', 1)->get(),
        ]);
    }

    public function store(StoreNewsRequest $request)
    {
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? []);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['category_id'] = $category_id;
        $data['slug'] = SlugHelper::generateUniqueSlug(News::class, $request->title);

        $news = News::create($data);

        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($news, $request->file('images'), $request->alts);
        }

        if ($request->hasFile('video')) {
            $this->mediaService->handleVideoUpload($news, $request->file('video'));
        }

        $this->logHelper->logActivity('create', 'news', 'Menambahkan berita ' . $news->title);
        return redirect()->route('dashboard.news.index')->with('success', 'Berita Berhasil Ditambahkan');
    }

    public function edit(News $news)
    {
        $categories = $this->categoryService->getCategoriesWithUsageCounts(1);

        return Inertia::render('Dashboard/News/Edit', [
            'news' => $news->load('images', 'category'),
            'categories' => $categories,
            'unused_categories' => $this->categoryService->getUnusedCategories(1),
        ]);
    }

    public function update(UpdateNewsRequest $request, News $news)
    {
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? []);

        if (!empty($request->category['deleting_category_id'])) {
            $deletionResults = $this->categoryService->handleCategoryDeletion($request->category['deleting_category_id']);
            session()->flash('categoryDeletionResults', $deletionResults);
        }

        $data = $request->all();
        $data['category_id'] = $category_id;
        $data['slug'] = SlugHelper::generateUniqueSlug(News::class, $request->title);

        // Update news record
        $news->update($data);

        // Handle removed images
        if ($request->removed_images) {
            $this->mediaService->handleImageRemovals($news, $request->removed_images);
        }

        // Handle updated alt texts for existing images
        if ($request->updated_existing_images) {
            $this->mediaService->handleImageAltTextUpdates($news, $request->updated_existing_images);
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $this->mediaService->handleImageUploads($news, $request->file('images'), $request->alts);
        }

        // Handle video upload if needed
        if ($request->hasFile('video')) {
            $this->mediaService->handleVideoUpload($news, $request->file('video'));
        }

        $this->logHelper->logActivity('edit', 'news', 'Memperbarui berita ' . $news->title);
        return redirect()->route('dashboard.news.index')->with('success', 'Berita Berhasil Diperbarui');
    }

    public function archive()
    {
        News::onlyTrashed()->with('category')->get();
        return Inertia::render('Dashboard/News/Archive', [
            'news' => News::onlyTrashed()->with('category')->get(),
        ]);
    }

    public function restore(News $news)
    {
        $news->restore();
        $this->logHelper->logActivity('restore', 'news', 'Memulihkan berita ' . $news->title);
        return redirect()->route('dashboard.news.archive')->with('success', 'Berita Berhasil Dipulihkan');
    }

    public function destroy(News $news)
    {
        if ($news->trashed()) {
            $this->mediaService->deleteAllMedia($news);
            $news->forceDelete();

            $this->logHelper->logActivity('delete', 'news', 'Menghapus berita ' . $news->title);
            return redirect()->route('dashboard.news.archive')->with('success', 'Berita Berhasil Dihapus');
        } else {
            $news->delete();

            $this->logHelper->logActivity('archive', 'news', 'Mengarsipkan berita ' . $news->title);
            return redirect()->route('dashboard.news.index')->with('success', 'Berita Berhasil Diarsipkan');
        }
    }
}
