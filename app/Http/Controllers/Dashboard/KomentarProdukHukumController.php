<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KomentarProdukHukum;
use App\Models\ProdukHukum;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KomentarProdukHukumController extends Controller
{
    public function index(Request $request)
    {
        $query = KomentarProdukHukum::with(['produk_hukum', 'parent']);

        // Handle produk_hukum_id filter (when coming from produk hukum list)
        if ($request->filled('produk_hukum_id')) {
            $query->where('id_produk_hukum', $request->produk_hukum_id);
        }

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('komentar', 'like', "%{$search}%")
                  ->orWhere('nama_user', 'like', "%{$search}%")
                  ->orWhere('email_user', 'like', "%{$search}%")
                  ->orWhereHas('produk_hukum', function ($produkHukumQuery) use ($search) {
                      $produkHukumQuery->where('title', 'like', "%{$search}%");
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
        $pendingCommentsCount = KomentarProdukHukum::where('is_approved', false)->count();

        // Clean up filters - only include is_approved, exclude produk_hukum_id from display filters
        $filters = array_filter([
            'is_approved' => $request->is_approved ?? '',
        ], function($value) {
            return $value !== '' && $value !== null;
        });

        return Inertia::render('Dashboard/ModerasiKomentarProdukHukum/Index', [
            'comments' => $query->paginate(25)->withQueryString(),
            'search' => $request->search ?? '',
            'filters' => $filters,
            'sort' => $sortField,
            'direction' => $sortDirection,
            'pendingCommentsCount' => $pendingCommentsCount,
        ]);
    }

    public function approve(KomentarProdukHukum $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Komentar berhasil disetujui');
    }

    public function destroy(KomentarProdukHukum $comment)
    {
        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus');
    }

    public function documentsIndex(Request $request)
    {
        $query = ProdukHukum::withCount([
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
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file', 'like', "%{$search}%");
            });
        }

        // Handle is_moderate filter - convert string to boolean
        if ($request->filled('is_moderate')) {
            $isModerate = $request->is_moderate === 'true';
            $query->where('is_moderate', $isModerate);
        }

        // Handle sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['title', 'created_at', 'comments_count', 'pending_comments_count'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get pending comments count for badge
        $pendingCommentsCount = KomentarProdukHukum::where('is_approved', false)->count();

        // Clean up filters - only include non-empty values
        $filters = array_filter([
            'is_moderate' => $request->is_moderate ?? '',
        ], function($value) {
            return $value !== '' && $value !== null;
        });

        return Inertia::render('Dashboard/ModerasiKomentarProdukHukum/IndexProdukHukum', [
            'produkHukum' => $query->paginate(25)->withQueryString(),
            'search' => $request->search ?? '',
            'filters' => $filters,
            'sort' => $sortField,
            'direction' => $sortDirection,
            'pendingCommentsCount' => $pendingCommentsCount,
        ]);
    }

    public function edit(Request $request, ProdukHukum $produkHukum)
    {
        $query = KomentarProdukHukum::with(['parent'])
            ->where('id_produk_hukum', $produkHukum->id);

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
        $pendingCommentsCount = KomentarProdukHukum::where('is_approved', false)->count();

        return Inertia::render('Dashboard/ModerasiKomentarProdukHukum/Edit', [
            'produkHukum' => $produkHukum,
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
