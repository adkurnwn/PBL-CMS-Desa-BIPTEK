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
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        // Get request parameters for search and sorting
        $search = $request->input('search');
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        
        // Get filter parameters
        $filters = [];
        if ($request->has('category_id')) {
            $filters['category_id'] = $request->input('category_id');
        }
        // Add filters for is_draft
        if ($request->has('is_draft')) {
            $filters['is_draft'] = $request->input('is_draft');
        }
        // Add filters for is_moderate
        if ($request->has('is_moderate')) {
            $filters['is_moderate'] = $request->input('is_moderate');
        }
        // Add filter for date range
        if ($request->has('date_filter')) {
            $filters['date_filter'] = $request->input('date_filter');
        }
        
        // Start building the query
        $query = News::with(['category', 'images']); // Include images relation
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Apply category filter if provided
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        // Apply is_draft filter if provided
        if (isset($filters['is_draft'])) {
            $query->where('is_draft', $filters['is_draft']);
        }
        
        // Apply is_moderate filter if provided
        if (isset($filters['is_moderate'])) {
            $query->where('is_moderate', $filters['is_moderate']);
        }
        
        // Apply date filter if provided
        if (isset($filters['date_filter'])) {
            $dateFilter = $filters['date_filter'];
            
            switch ($dateFilter) {
                case 'today':
                    // Filter for today
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                    
                case 'this_week':
                    // Filter for this week
                    $query->whereBetween('created_at', [
                        now()->startOfWeek()->toDateString(),
                        now()->endOfWeek()->toDateString()
                    ]);
                    break;
                    
                case 'this_month':
                    // Filter for this month
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                    
                case 'this_year':
                    // Filter for this year
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        // Apply sorting - first by draft status (drafts first), then by the user's sort field
        $query->orderBy('is_draft', 'desc') // Put drafts (is_draft=1) first
              ->orderBy($sortField, $sortDirection);
        
        // Get paginated results - 10 items per page
        $news = $query->paginate(25)->withQueryString();
        
        // Get all categories for filtering
        $categories = Category::where('category_type', 1)->get();
        
        return Inertia::render('Dashboard/News/Index', [
            'news' => $news,
            'search' => $search,
            'sort' => $sortField,
            'direction' => $sortDirection,
            'filters' => $filters,
            'categories' => $categories,
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
        
        // Generate a truly unique slug with a timestamp suffix to prevent duplicates
        $baseSlug = SlugHelper::generateUniqueSlug(News::class, $request->title);
        $data['slug'] = $baseSlug . '-' . time();

        // Store content temporarily without processing
        $originalContent = $data['content'];
        
        $news = News::create($data);

        // Handle image uploads and get the uploaded image paths
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            $uploadedImages = $this->mediaService->handleImageUploads($news, $request->file('images'), $request->alts);
        }

        // Process content to replace image placeholders with actual HTML
        $processedContent = $this->processImagePlaceholdersInContent($originalContent, $uploadedImages);
        
        // Update news with processed content
        $news->update(['content' => $processedContent]);

        if ($request->hasFile('video')) {
            $this->mediaService->handleVideoUpload($news, $request->file('video'));
        }

        $this->logHelper->logActivity('create', 'news', 'Menambahkan berita ' . $news->title);
        return redirect()->route('dashboard.news.index')->with('success', 'Berita Berhasil Ditambahkan');
    }

    public function edit($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        $categories = $this->categoryService->getCategoriesWithUsageCounts(1);

        return Inertia::render('Dashboard/News/Edit', [
            'news' => $news->load('images', 'category'),
            'categories' => $categories,
            'unused_categories' => $this->categoryService->getUnusedCategories(1),
        ]);
    }

    public function update(UpdateNewsRequest $request, $slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        $category_id = $this->categoryService->handleCategoryCreationOrUpdate($request->category ?? []);

        if (!empty($request->category['deleting_category_id'])) {
            $deletionResults = $this->categoryService->handleCategoryDeletion($request->category['deleting_category_id']);
            session()->flash('categoryDeletionResults', $deletionResults);
        }

        $data = $request->all();
        $data['category_id'] = $category_id;
        
        // For updates, only change slug if title changed, and ensure uniqueness
        if ($news->title != $request->title) {
            $baseSlug = SlugHelper::generateUniqueSlug(News::class, $request->title);
            $data['slug'] = $baseSlug . '-' . time();
        } else {
            $data['slug'] = $news->slug;
        }

        // Store content temporarily without processing
        $originalContent = $data['content'];

        // Handle removed images
        if ($request->removed_images) {
            $this->mediaService->handleImageRemovals($news, $request->removed_images);
        }

        // Handle updated alt texts for existing images
        if ($request->updated_existing_images) {
            $this->mediaService->handleImageAltTextUpdates($news, $request->updated_existing_images);
        }

        // Handle new images and get uploaded paths
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            $uploadedImages = $this->mediaService->handleImageUploads($news, $request->file('images'), $request->alts);
        }

        // Process content to replace image placeholders with actual HTML
        $processedContent = $this->processImagePlaceholdersInContent($originalContent, $uploadedImages);
        $data['content'] = $processedContent;

        // Update news record
        $news->update($data);

        // Handle video upload if needed
        if ($request->hasFile('video')) {
            $this->mediaService->handleVideoUpload($news, $request->file('video'));
        }

        $this->logHelper->logActivity('edit', 'news', 'Memperbarui berita ' . $news->title);
        return redirect()->route('dashboard.news.index')->with('success', 'Berita Berhasil Diperbarui');
    }

    /**
     * Process image placeholders in content and replace with HTML img tags
     */
    private function processImagePlaceholdersInContent($content, $uploadedImages)
    {
        $processedContent = $content;
        
        // Replace placeholders with actual image HTML
        foreach ($uploadedImages as $index => $imageData) {
            $placeholder = "{{IMAGE_$index}}";
            $imageHtml = sprintf(
                '<img src="%s" alt="%s" class="max-w-full h-auto block my-2" />',
                asset('storage/' . $imageData['path']),
                htmlspecialchars($imageData['alt'] ?? '')
            );
            
            $processedContent = str_replace($placeholder, $imageHtml, $processedContent);
        }
        
        // Clean up any remaining placeholder patterns that might not have been processed
        $processedContent = preg_replace('/\{\{IMAGE_\d+\}\}/', '', $processedContent);
        
        // Clean up any remaining blob URLs that might have slipped through
        $processedContent = preg_replace('/<img[^>]*src="blob:[^"]*"[^>]*>/', '', $processedContent);
        
        // Remove any data-is-placeholder attributes from remaining images
        $processedContent = preg_replace('/\s*data-is-placeholder="[^"]*"/', '', $processedContent);
        $processedContent = preg_replace('/\s*data-image-id="[^"]*"/', '', $processedContent);
        $processedContent = preg_replace('/\s*data-blob-url="[^"]*"/', '', $processedContent);
        
        // Replace preview classes with final classes for full-width display
        $processedContent = preg_replace('/class="richtext-preview-image"/', 'class="max-w-full h-auto block my-2"', $processedContent);
        
        return $processedContent;
    }

    public function archive(Request $request)
    {
        // Get request parameters for search and sorting
        $search = $request->input('search');
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        
        // Get filter parameters
        $filters = [];
        if ($request->has('category_id')) {
            $filters['category_id'] = $request->input('category_id');
        }
        // Add filters for is_draft
        if ($request->has('is_draft')) {
            $filters['is_draft'] = $request->input('is_draft');
        }
        // Add filters for is_moderate
        if ($request->has('is_moderate')) {
            $filters['is_moderate'] = $request->input('is_moderate');
        }
        // Add filter for date range
        if ($request->has('date_filter')) {
            $filters['date_filter'] = $request->input('date_filter');
        }
        
        // Start building the query
        $query = News::onlyTrashed()->with(['category', 'images']); // Include images relation
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Apply category filter if provided
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        // Apply is_draft filter if provided
        if (isset($filters['is_draft'])) {
            $query->where('is_draft', $filters['is_draft']);
        }
        
        // Apply is_moderate filter if provided
        if (isset($filters['is_moderate'])) {
            $query->where('is_moderate', $filters['is_moderate']);
        }
        
        // Apply date filter if provided
        if (isset($filters['date_filter'])) {
            $dateFilter = $filters['date_filter'];
            
            switch ($dateFilter) {
                case 'today':
                    // Filter for today
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                    
                case 'this_week':
                    // Filter for this week
                    $query->whereBetween('created_at', [
                        now()->startOfWeek()->toDateString(),
                        now()->endOfWeek()->toDateString()
                    ]);
                    break;
                    
                case 'this_month':
                    // Filter for this month
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                    
                case 'this_year':
                    // Filter for this year
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        // Apply sorting - first by draft status (drafts first), then by the user's sort field
        $query->orderBy('is_draft', 'desc') // Put drafts (is_draft=1) first
              ->orderBy($sortField, $sortDirection);
    
        // Get paginated results - 10 items per page
        $news = $query->paginate(25)->withQueryString();
        
        // Get all categories for filtering
        $categories = Category::where('category_type', 1)->get();
        
        return Inertia::render('Dashboard/News/Archive', [
            'news' => $news,
            'search' => $search,
            'sort' => $sortField,
            'direction' => $sortDirection,
            'filters' => $filters,
            'categories' => $categories,
        ]);
    }

    public function restore($slug)
    {
        $news = News::withTrashed()->where('slug', $slug)->firstOrFail();
        $news->restore();
        $this->logHelper->logActivity('restore', 'news', 'Memulihkan berita ' . $news->title);
        return redirect()->route('dashboard.news.archive')->with('success', 'Berita Berhasil Dipulihkan');
    }

    public function destroy($slug)
    {
        $news = News::withTrashed()->where('slug', $slug)->firstOrFail();

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
