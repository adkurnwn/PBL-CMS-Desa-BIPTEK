<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/News/Index', [
            'news' => News::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/News/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required',
            'image' => 'required|image|max:1024|mimes:jpg,png,jpeg',
            'video' => 'sometimes|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean', // Change from is_moderated
        ]);

        $data = $request->all();

        // Remove files from initial data
        unset($data['image']);
        unset($data['video']);

        // Create news record
        $news = News::create($data);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();
            $image->storeAs('public/news', $imageName);

            // Update the image path
            $news->update([
                'image' => 'news/' . $imageName
            ]);
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = $video->hashName();
            $video->storeAs('public/news', $videoName);

            // Update the video path
            $news->update([
                'video' => 'news/' . $videoName
            ]);
        }

        return redirect()->route('admin.news');
    }

    public function edit(News $news)
    {
        return Inertia::render('Admin/News/Edit', [
            'news' => $news,
        ]);
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required',
            'image' => 'nullable|sometimes|image|max:1024|mimes:jpg,png,jpeg',
            'video' => 'nullable|sometimes|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
        ]);

        $data = $request->all();

        // Remove files from data if not uploading new ones
        if (!$request->hasFile('image')) {
            unset($data['image']);
        }

        if (!$request->hasFile('video')) {
            unset($data['video']);
        }

        // First update non-file data
        $news->update($data);

        // Then handle file uploads and update the database with file names
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();
            $image->storeAs('public/news', $imageName);

            // Update the image path in database
            $news->update([
                'image' => 'news/' . $imageName
            ]);
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = $video->hashName();
            $video->storeAs('public/news', $videoName);

            // Update the video path in database
            $news->update([
                'video' => 'news/' . $videoName
            ]);
        }

        return redirect()->route('admin.news');
    }
}
