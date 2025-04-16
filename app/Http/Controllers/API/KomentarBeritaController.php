<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use App\Models\KomentarBerita;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KomentarBeritaController extends Controller
{
    public function index($newsId)
    {
        $news = News::findOrFail($newsId);
        
        $comments = KomentarBerita::where('id_berita', $newsId)
            ->when($news->is_moderate, function($query) {
                // If news is moderated, only show approved comments
                $query->where('is_approved', true);
            })
            ->whereNull('id_ref_komentar')
            ->with(['replies' => function($query) use ($news) {
                if ($news->is_moderate) {
                    $query->where('is_approved', true);
                }
            }])
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_berita' => 'required|exists:news,id',
            'nama_user' => 'required|string|max:50',
            'komentar' => 'required|string',
            'id_ref_komentar' => 'nullable|exists:komentar_berita,id'
        ]);

        $news = News::findOrFail($request->id_berita);

        $comment = KomentarBerita::create([
            'id_berita' => $request->id_berita,
            'nama_user' => $request->nama_user,
            'komentar' => $request->komentar,
            'id_ref_komentar' => $request->id_ref_komentar,
            'is_approved' => !$news->is_moderate // Auto-approve if news is not moderated
        ]);

        $message = $news->is_moderate 
            ? 'Comment submitted and waiting for approval'
            : 'Comment posted successfully';

        return response()->json([
            'message' => $message,
            'comment' => $comment
        ], 201);
    }
}
