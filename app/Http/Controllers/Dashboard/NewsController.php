<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\News;
use Inertia\Inertia;
use App\Models\Image;
use App\Models\Gallery;
use App\Models\Category;
use Illuminate\Support\Str;
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
            'categories' => Category::where('category_type', 1)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'content' => 'required',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required|string|max:255',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
            'is_draft' => 'required|boolean',
        ];

        if ($request->filled('category.new_category')) {
            $rules['category.new_category'] = 'required|string|max:255';
        } else {
            $rules['category.category_id'] = 'required|exists:categories,id';
        }

        if ($request->filled('category.edit_category_id') && $request->filled('category.new_category_name')) {
            $rules['category.edit_category_id'] = 'required|exists:categories,id';
            $rules['category.new_category_name'] = 'required|string|max:255';
        }

        $request->validate($rules);

        if ($request->filled('category.new_category')) {
            $category = Category::create(['name' => $request->category['new_category']]);
            $category_id = $category->id;
        } else if ($request->filled('category.edit_category_id') && $request->filled('category.new_category_name')) {
            // Handle category editing
            $category = Category::find($request->category['edit_category_id']);
            $category->name = $request->category['new_category_name'];
            $category->save();
            $category_id = $category->id;
        } else {
            $category_id = $request->category['category_id'];
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['category_id'] = $category_id;
        $data['slug'] = Str::of($request->title)->slug('-');

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

        return redirect()->route('dashboard.news')->with('success', 'News Added Successfully');
    }

    public function edit(News $news)
    {
        // Get all categories and their usage count
        $categories = Category::where('category_type', 1)->get();

        // Add usage count to each category
        foreach ($categories as $category) {
            $newsCount = News::where('category_id', $category->id)->count();
            $galleryCount = Gallery::where('category_id', $category->id)->count();
            $category->usage_count = $newsCount + $galleryCount;
        }

        return Inertia::render('Dashboard/News/Edit', [
            'news' => $news->load('images', 'category'),
            'categories' => $categories,
            'unused_categories' => $categories->filter(function ($category) {
                return $category->usage_count === 0;
            })->values()
        ]);
    }

    public function update(Request $request, News $news)
    {
        $rules = [
            'title' => 'required',
            'content' => 'required',
            'images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required_with:images.*|string|max:255',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:images,id',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
            'is_draft' => 'required|boolean',
        ];

        // Add validation for category selection
        if ($request->filled('category.new_category')) {
            $rules['category.new_category'] = 'required|string|max:255';
        } else if ($request->filled('category.edit_category_id') && $request->filled('category.new_category_name')) {
            $rules['category.edit_category_id'] = 'required|exists:categories,id';
            $rules['category.new_category_name'] = 'required|string|max:255';
        } else {
            $rules['category.category_id'] = 'required|exists:categories,id';
        }

        if ($request->filled('category.deleting_category_id')) {
            $rules['category.deleting_category_id.*'] = 'required|exists:categories,id';
        }

        $request->validate($rules);

        // Handle category
        if ($request->filled('category.new_category')) {
            $category = Category::create(['name' => $request->category['new_category']]);
            $category_id = $category->id;
        } else if ($request->filled('category.edit_category_id') && $request->filled('category.new_category_name')) {
            // Handle category editing
            $category = Category::find($request->category['edit_category_id']);
            $category->name = $request->category['new_category_name'];
            $category->save();
            $category_id = $category->id;
        } else {
            $category_id = $request->category['category_id'];
        }

        if ($request->filled('category.deleting_category_id')) {
            // Handle category deletion
            $deletionResults = [];

            foreach ($request->category['deleting_category_id'] as $deletingCategoryId) {
                $category = Category::find($deletingCategoryId);
                if ($category) {
                    // Check if the category is being used by any news
                    $newsCount = News::where('category_id', $category->id)->count();
                    $galleryCount = Gallery::where('category_id', $category->id)->count();
                    $inUseCount = $newsCount + $galleryCount;

                    if ($inUseCount > 0) {
                        // Skip deletion and add to results
                        $deletionResults[$category->name] = "Cannot delete: in use by {$inUseCount} news and gallery";
                    } else {
                        $category->delete();
                        $deletionResults[$category->name] = "Deleted successfully";
                    }
                }
            }
        }

        $data = $request->all();
        $data['category_id'] = $category_id;
        $data['slug'] = Str::of($request->title)->slug('-');

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

        return redirect()->route('dashboard.news')->with('success', 'News Updated Successfully');
    }


    public function archive()
    {
        News::onlyTrashed()->with('category')->get();
        return Inertia::render('Dashboard/News/Archive', [
            'news' => News::onlyTrashed()->with('category')->get(),
        ]);
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('dashboard.news')->with('success', 'News Deleted Successfully');
    }
}
