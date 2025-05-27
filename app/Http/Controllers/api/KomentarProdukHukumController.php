<?php

namespace App\Http\Controllers\api;

use App\Models\ProdukHukum;
use App\Models\KomentarProdukHukum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KomentarProdukHukumController extends Controller
{
    public function index($slug)
    {
        $produkHukum = ProdukHukum::where('slug', $slug)->firstOrFail();

        $comments = KomentarProdukHukum::where('id_produk_hukum', $produkHukum->id)
            ->when($produkHukum->is_moderate, function ($query) {
                $query->where('is_approved', true);
            })
            ->whereNull('id_ref_komentar')
            ->withCount('replies')
            ->get();

        return response()->json($comments);
    }

    public function getReplies($slug, $commentId)
    {
        $produkHukum = ProdukHukum::where('slug', $slug)->firstOrFail();

        $replies = KomentarProdukHukum::where('id_produk_hukum', $produkHukum->id)
            ->whereNotNull('id_ref_komentar')
            ->when($produkHukum->is_moderate, function ($query) {
                $query->where('is_approved', true);
            })
            ->with(['parent', 'replies'])
            ->get();

        $replies = $this->addParentUserNames($replies);

        return response()->json($replies);
    }

    private function countAllReplies($reply)
    {
        $count = $reply->replies ? $reply->replies->count() : 0;
        if ($reply->replies) {
            foreach ($reply->replies as $child) {
                $count += $this->countAllReplies($child);
            }
        }
        return $count;
    }

    private function addParentUserNames($replies)
    {
        return $replies->map(function ($reply) {
            $reply->parent_user = $reply->parent ? $reply->parent->nama_user : 'Unknown';
            $reply->replies_count = $this->countAllReplies($reply); // gunakan rekursif
            if ($reply->replies && $reply->replies->count() > 0) {
                $reply->replies = $this->addParentUserNames($reply->replies);
            }
            return $reply;
        });
    }

    public function store(Request $request)
    {
        $rules = [
            'id_produk_hukum' => 'required|string',
            'nama_user' => 'required|string|max:50',
            'komentar' => 'required|string',
            'id_ref_komentar' => 'nullable|exists:komentar_produk_hukum,id',
            'captcha' => 'required|captcha_api:' . $request->code . ',default'
        ];

        $request->validate($rules, [
            'captcha.captcha_api' => 'Invalid captcha code'
        ]);

        $produkhukum = ProdukHukum::where('slug', $request->id_produk_hukum)->firstOrFail();

        $comment = KomentarProdukHukum::create([
            'id_produk_hukum' => $produkhukum->id,
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
