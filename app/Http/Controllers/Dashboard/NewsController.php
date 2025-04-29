<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\News;
use App\Models\Category;
use Inertia\Inertia;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/News/Index', [
            'news' => News::with('category')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/News/Create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $validate = [
            'title' => 'required',
            'content' => 'required',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required|string|max:255',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
            'is_draft' => 'required|boolean',
        ];

        if ($request->filled('new_category')) {
            $validate['new_category'] = 'required|string|max:255';
        } else {
            $validate['category_id'] = 'required|exists:categories,id';
        }

        $request->validate($validate);

        if ($request->filled('new_category')) {
            $category = Category::create(['name' => $request->new_category]);
            $category_id = $category->id;
        } else {
            $category_id = $request->category_id;
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['category_id'] = $category_id;

        unset($data['images']);
        unset($data['alts']);
        unset($data['video']);
        unset($data['new_category']);

        $news = News::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('news-images', 'public');

                $news->images()->create([
                    'path' => $path,
                    'alt' => $request->alts[$index],
                    'is_visible' => true
                ]);
            }
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('news-videos', 'public');
            $news->update(['video' => $videoPath]);
        }

        return redirect()->route('dashboard.news');
    }

    public function edit(News $news)
    {
        return Inertia::render('Dashboard/News/Edit', [
            'news' => $news->load('images', 'category'),
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required_with:images.*|string|max:255',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:images,id',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
            'is_draft' => 'required|boolean',
        ]);

        // Handle category
        if ($request->filled('new_category')) {
            $category = Category::create(['name' => $request->new_category]);
            $category_id = $category->id;
        } else {
            $category_id = $request->category_id;
        }

        $data = $request->all();
        $data['category_id'] = $category_id; // Set category_id

        // Remove unnecessary data
        unset($data['images']);
        unset($data['alts']);
        unset($data['removed_images']);
        unset($data['video']);
        unset($data['new_category']);

        // Update news record
        $news->update($data);

        // Handle removed images
        if ($request->removed_images) {
            foreach ($request->removed_images as $imageId) {
                $image = Image::find($imageId);
                if ($image && $image->news_id === $news->id) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('news-images', 'public');

                $news->images()->create([
                    'path' => $path,
                    'alt' => $request->alts[$index],
                    'is_visible' => true
                ]);
            }
        }

        // Handle video upload if needed
        if ($request->hasFile('video')) {
            if ($news->video) {
                Storage::disk('public')->delete($news->video);
            }
            $videoPath = $request->file('video')->store('news-videos', 'public');
            $news->update(['video' => $videoPath]);
        }

        return redirect()->route('dashboard.news');
    }
}
