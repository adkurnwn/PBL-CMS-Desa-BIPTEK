<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KomentarBerita;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KomentarBeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = KomentarBerita::with(['news', 'parent']);

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
}
