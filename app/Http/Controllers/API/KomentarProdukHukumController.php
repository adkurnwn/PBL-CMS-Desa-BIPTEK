<?php

namespace App\Http\Controllers\api;

use App\Models\ProdukHukum;
use App\Models\KomentarProdukHukum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KomentarProdukHukumController extends Controller
{
    public function index($newsId)
    {
        $news = ProdukHukum::findOrFail($newsId);

        $comments = KomentarProdukHukum::where('id_produk_hukum', $newsId)
            ->when($news->is_moderate, function($query) {
                $query->where('is_approved', true);
            })
            ->whereNull('id_ref_komentar')
            ->withCount('replies')
            ->get();

        return response()->json($comments);
    }

    public function getReplies($produkhukumId, $commentId)
    {
        $produkhukum = ProdukHukum::findOrFail($produkhukumId);

        // Only get direct replies to the comment
        $replies = KomentarProdukHukum::where('id_produk_hukum', $produkhukumId)
            ->where('id_ref_komentar', $commentId)
            ->when($produkhukum->is_moderate, function($query) {
                $query->where('is_approved', true);
            })
            ->with(['parent', 'replies'])
            ->get();

        return response()->json($this->addParentUserNames($replies));
    }

    private function addParentUserNames($replies)
    {
        return $replies->map(function($reply) {
            $reply->parent_user = $reply->parent ? $reply->parent->nama_user : 'Unknown';
            if ($reply->replies && $reply->replies->count() > 0) {
                $reply->replies = $this->addParentUserNames($reply->replies);
            }
            return $reply;
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk_hukum' => 'required|exists:produk_hukum,id',
            'nama_user' => 'required|string|max:50',
            'komentar' => 'required|string',
            'id_ref_komentar' => 'nullable|exists:komentar_produk_hukum,id'
        ]);

        $produkhukum = ProdukHukum::findOrFail($request->id_produk_hukum);

        $comment = KomentarProdukHukum::create([
            'id_produk_hukum' => $request->id_produk_hukum,
            'nama_user' => $request->nama_user,
            'komentar' => $request->komentar,
            'id_ref_komentar' => $request->id_ref_komentar,
            'is_approved' => !$produkhukum->is_moderate
        ]);

        if ($comment->id_ref_komentar) {
            $comment->load('parent');
            $comment->parent_user = $comment->parent->nama_user;
        }

        $message = $produkhukum->is_moderate
            ? 'Komentar telah dikirim dan menunggu persetujuan'
            : 'Komentar berhasil dikirim';

        return response()->json([
            'message' => $message,
            //'comment' => $comment
        ], 201);
    }
}
