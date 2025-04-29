<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\KomentarProdukHukum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KomentarProdukHukumController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/ModerasiKomentarProdukHukum/Index', [
            'comments' => KomentarProdukHukum::with(['produk_hukum', 'parent'])
                ->orderBy('created_at', 'desc')
                ->paginate(20)
        ]);
    }

    public function approve(KomentarProdukHukum $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved successfully');
    }

    public function destroy(KomentarProdukHukum $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully');
    }
}
