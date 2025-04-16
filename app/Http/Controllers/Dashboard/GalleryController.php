<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Image;
use App\Models\Gallery;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Gallery/Index', [
            'galleries' => Gallery::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Gallery/Create', [
            'categories' => Category::where('category_type', 1)->get()
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required|string|max:255'
        ];

        if ($request->filled('category.new_category')) {
            $rules['category.new_category'] = 'required|string|max:255';
        } else {
            $rules['category.category_id'] = 'required|exists:categories,id';
        }

        $request->validate($rules);

        if ($request->filled('category.new_category')) {
            $category = Category::create(['name' => $request->category['new_category']]);
            $category_id = $category->id;
        } else {
            $category_id = $request->category['category_id'];
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['category_id'] = $category_id;

        $gallery = Gallery::create($data);

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('gallery-images', 'public');

                // Create image record
                $gallery->images()->create([
                    'path' => $path,
                    'alt' => $request->alts[$index] ?? $gallery->title,
                    'is_visible' => true
                ]);
            }
        }

        return redirect()->route('dashboard.galleries');
    }

    public function edit(Gallery $gallery)
    {
        return Inertia::render('Dashboard/Gallery/Edit', [
            'gallery' => $gallery->load('images', 'category'),
            'categories' => Category::where('category_type', 1)->get()
        ]);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required_with:images.*|string|max:255',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:images,id'
        ]);

        // Update gallery details
        if ($request->filled('category.new_category')) {
            $category = Category::create(['name' => $request->category['new_category']]);
            $category_id = $category->id;
        } else {
            $category_id = $request->category['category_id'];
        }

        $data = $request->all();
        $data['category_id'] = $category_id;

        // Update news record
        $gallery->update($data);

        // Handle image removals
        if ($request->removed_images) {
            foreach ($request->removed_images as $imageId) {
                $image = Image::find($imageId);
                if ($image && $image->gallery_id === $gallery->id) {
                    // Delete the physical file
                    Storage::disk('public')->delete($image->path);
                    // Delete the database record
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('gallery-images', 'public');

                // Create new image record
                $gallery->images()->create([
                    'path' => $path,
                    'alt' => $request->alts[$index] ?? $gallery->title,
                    'is_visible' => true
                ]);
            }
        }

        return redirect()->route('dashboard.galleries')
            ->with('success', 'Gallery updated successfully');
    }
}
