<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\News;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Remove files from initial data
        unset($data['image']);
        unset($data['video']);

        // Create news record
        $news = News::create($data);

        // Handle image upload and create image record
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news-images', 'public');

            // Create image record in images table
            $news->images()->create([
                'path' => $path,
                'alt' => $news->title, // Using news title as alt text
                'is_visible' => true
            ]);
        }

        // Handle video upload if needed
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('news-videos', 'public');
            $news->update(['video' => $videoPath]);
        }

        return redirect()->route('dashboard.news');
    }

    public function edit(News $news)
    {
        return Inertia::render('Dashboard/News/Edit', [
            'news' => $news->load('images'),
        ]);
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'video' => 'nullable|sometimes|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
        ]);

        $data = $request->all();
        unset($data['video']);
        unset($data['image']);

        // Update news record
        $news->update($data);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($news->images()->exists()) {
                $oldImage = $news->images()->first();
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }

            // Store new image
            $path = $request->file('image')->store('news-images', 'public');

            // Create new image record
            $news->images()->create([
                'path' => $path,
                'alt' => $news->title,
                'is_visible' => true
            ]);
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = $video->hashName();
            $video->storeAs('public/news', $videoName);

            $news->update([
                'video' => 'news/' . $videoName
            ]);
        }

        return redirect()->route('dashboard.news');
    }
}
