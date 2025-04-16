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
                $query->where('is_approved', true);
            })
            ->whereNull('id_ref_komentar')
            ->withCount('replies')
            ->get();

        return response()->json($comments);
    }

    public function getReplies($newsId, $commentId)
    {
        $news = News::findOrFail($newsId);
        
        $replies = KomentarBerita::where('id_berita', $newsId)
            ->where(function($query) use ($commentId) {
                $query->where('id_ref_komentar', $commentId)
                      ->orWhereHas('parent', function($q) use ($commentId) {
                          $q->where('id_ref_komentar', $commentId)
                            ->orWhereHas('parent', function($q2) use ($commentId) {
                                $q2->where('id_ref_komentar', $commentId);
                            });
                      });
            })
            ->when($news->is_moderate, function($query) {
                $query->where('is_approved', true);
            })
            ->with(['parent', 'replies.parent'])
            ->get();

        return response()->json($this->addParentUserNames($replies));
    }

    private function addParentUserNames($replies)
    {
        return $replies->map(function($reply) {
            $reply->parent_user = $reply->parent ? $reply->parent->nama_user : 'Unknown';
            if ($reply->replies) {
                $reply->replies = $this->addParentUserNames($reply->replies);
            }
            return $reply;
        });
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
            'is_approved' => !$news->is_moderate
        ]);

        if ($comment->id_ref_komentar) {
            $comment->load('parent');
            $comment->parent_user = $comment->parent->nama_user;
        }

        $message = $news->is_moderate 
            ? 'Comment submitted and waiting for approval'
            : 'Comment posted successfully';

        return response()->json([
            'message' => $message,
            'comment' => $comment
        ], 201);
    }
}
