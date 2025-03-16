<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\News;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/News/Index', [
            'news' => News::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/News/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required',
            'video' => 'sometimes|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        unset($data['video']);

        // Create news record
        $news = News::create($data);

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = $video->hashName();
            $video->storeAs('public/news', $videoName);

            // Update the video path
            $news->update([
                'video' => 'news/' . $videoName
            ]);
        }

        return redirect()->route('dashboard.news');
    }

    public function edit(News $news)
    {
        return Inertia::render('Dashboard/News/Edit', [
            'news' => $news,
        ]);
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required',
            'video' => 'nullable|sometimes|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
        ]);

        $data = $request->all();

        if (!$request->hasFile('video')) {
            unset($data['video']);
        }

        // First update non-file data
        $news->update($data);

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = $video->hashName();
            $video->storeAs('public/news', $videoName);

            // Update the video path in database
            $news->update([
                'video' => 'news/' . $videoName
            ]);
        }

        return redirect()->route('dashboard.news');
    }
}
