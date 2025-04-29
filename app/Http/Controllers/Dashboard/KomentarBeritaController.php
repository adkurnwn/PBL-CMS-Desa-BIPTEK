<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KomentarBerita;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KomentarBeritaController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/ModerasiKomentar/Index', [
            'comments' => KomentarBerita::with(['news', 'parent'])
                ->orderBy('created_at', 'desc')
                ->paginate(20)
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
