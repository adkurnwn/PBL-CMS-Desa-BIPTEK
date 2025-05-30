<?php

namespace App\Http\Controllers\api;

use App\Models\News;
use App\Models\KomentarBerita;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KomentarBeritaController extends Controller
{
    public function index($slug)
    {
        $news = News::where('slug', $slug)
            ->where('is_draft', false)
            ->firstOrFail();

        $comments = KomentarBerita::where('id_berita', $news->id)
            ->when($news->is_moderate, function ($query) {
                $query->where('is_approved', true);
            })
            ->whereNull('id_ref_komentar')
            ->withCount('replies')
            ->get();

        return response()->json($comments);
    }

    public function getReplies($slug, $commentId)
    {
        $news = News::where('slug', $slug)
            ->where('is_draft', false)
            ->firstOrFail();

        $replies = KomentarBerita::where('id_berita', $news->id)
            ->where(function ($query) use ($commentId) {
                $query->where('id_ref_komentar', $commentId)
                    ->orWhereHas('parent', function ($q) use ($commentId) {
                        $q->where('id_ref_komentar', $commentId)
                            ->orWhereHas('parent', function ($q2) use ($commentId) {
                                $q2->where('id_ref_komentar', $commentId);
                            });
                    });
            })
            ->when($news->is_moderate, function ($query) {
                $query->where('is_approved', true);
            })
            ->with(['parent', 'replies.parent'])
            ->get();

        return response()->json($this->addParentUserNames($replies));
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
            $reply->replies_count = $this->countAllReplies($reply); // Use recursive count
            if ($reply->replies) {
                $reply->replies = $this->addParentUserNames($reply->replies);
            }
            return $reply;
        });
    }

    public function store(Request $request)
    {
        $rules = [
            'id_berita' => 'required|string',
            'nama_user' => 'required|string|max:50',
            'komentar' => 'required|string',
            'email_user' => 'required|email|max:50',
            'id_ref_komentar' => 'nullable|exists:komentar_berita,id',
            'captcha' => 'required|captcha_api:' . $request->code . ',default'
        ];

        $request->validate($rules, [
            'captcha.captcha_api' => 'Invalid captcha code'
        ]);

        $news = News::where('slug', $request->id_berita)
            ->where('is_draft', false)
            ->firstOrFail();

        $data = $request->all();
        $data['id_berita'] = $news->id;
        $data['is_approved'] = !$news->is_moderate;

        $comment = KomentarBerita::create($data);

        if ($comment->id_ref_komentar) {
            $comment->load('parent');
            $comment->parent_user = $comment->parent->nama_user;
        }

        $message = $news->is_moderate
            ? 'Comment submitted and waiting for approval'
            : 'Comment posted successfully';

        return response()->json([
            'message' => $message,
            //'comment' => $comment
        ], 201);
    }
}
