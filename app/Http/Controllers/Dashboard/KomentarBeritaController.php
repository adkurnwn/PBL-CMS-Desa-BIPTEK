<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KomentarBerita;
use App\Models\News;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KomentarBeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = KomentarBerita::with(['news', 'parent']);

        // Handle news_id filter (when coming from news list)
        if ($request->filled('news_id')) {
            $query->where('id_berita', $request->news_id);
        }

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('komentar', 'like', "%{$search}%")
                  ->orWhere('nama_user', 'like', "%{$search}%")
                  ->orWhere('email_user', 'like', "%{$search}%")
                  ->orWhereHas('news', function ($newsQuery) use ($search) {
                      $newsQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Handle status filter
        if ($request->filled('is_approved')) {
            if ($request->is_approved === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->is_approved === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Handle sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['komentar', 'nama_user', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get pending comments count for badge
        $pendingCommentsCount = KomentarBerita::where('is_approved', false)->count();

        return Inertia::render('Dashboard/ModerasiKomentar/Index', [
            'comments' => $query->paginate(25)->withQueryString(),
            'search' => $request->search ?? '',
            'filters' => [
                'is_approved' => $request->is_approved ?? '',
                'news_id' => $request->news_id ?? '',
            ],
            'sort' => $sortField,
            'direction' => $sortDirection,
            'pendingCommentsCount' => $pendingCommentsCount,
        ]);
    }

    public function approve(KomentarBerita $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved successfully');
    }

    public function destroy(KomentarBerita $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully');
    }

    public function newsIndex(Request $request)
    {
        $query = News::with(['category'])
            ->withCount([
                'comments',
                'comments as approved_comments_count' => function ($query) {
                    $query->where('is_approved', true);
                },
                'comments as pending_comments_count' => function ($query) {
                    $query->where('is_approved', false);
                }
            ]);

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Handle category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Handle is_moderate filter
        if ($request->filled('is_moderate')) {
            $isModerate = $request->is_moderate === 'true' || $request->is_moderate === true;
            $query->where('is_moderate', $isModerate);
        }

        // Handle sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['title', 'created_at', 'comments_count'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get pending comments count for badge
        $pendingCommentsCount = KomentarBerita::where('is_approved', false)->count();

        // Get all categories for filtering
        $categories = Category::where('category_type', 1)->get();

        return Inertia::render('Dashboard/ModerasiKomentar/IndexBerita', [
            'news' => $query->paginate(25)->withQueryString(),
            'search' => $request->search ?? '',
            'filters' => [
                'category_id' => $request->category_id ?? '',
                'is_moderate' => $request->is_moderate ?? '',
            ],
            'sort' => $sortField,
            'direction' => $sortDirection,
            'pendingCommentsCount' => $pendingCommentsCount,
            'categories' => $categories,
        ]);
    }

    public function edit(Request $request, News $news)
    {
        $query = KomentarBerita::with(['parent'])
            ->where('id_berita', $news->id);

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('komentar', 'like', "%{$search}%")
                  ->orWhere('nama_user', 'like', "%{$search}%")
                  ->orWhere('email_user', 'like', "%{$search}%");
            });
        }

        // Handle status filter
        if ($request->filled('is_approved')) {
            if ($request->is_approved === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->is_approved === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Handle sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['komentar', 'nama_user', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get pending comments count for badge
        $pendingCommentsCount = KomentarBerita::where('is_approved', false)->count();

        // Load news with images
        $news->load(['images', 'category']);

        return Inertia::render('Dashboard/ModerasiKomentar/Edit', [
            'news' => $news,
            'comments' => $query->paginate(25)->withQueryString(),
            'search' => $request->search ?? '',
            'filters' => [
                'is_approved' => $request->is_approved ?? '',
            ],
            'sort' => $sortField,
            'direction' => $sortDirection,
            'pendingCommentsCount' => $pendingCommentsCount,
        ]);
    }
}
