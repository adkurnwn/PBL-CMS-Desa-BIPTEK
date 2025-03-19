<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

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
        return Inertia::render('Dashboard/Gallery/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required|in:news,event,infrastructure,nature',
            'description' => 'required',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required|string|max:255'
        ]);

        // Create gallery
        $gallery = Gallery::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
        ]);

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('gallery-images', 'public');

                // Create image record
                $gallery->images()->create([
                    'path' => $path,
                    'alt' => $request->alts[$index] ?? $gallery->title, // Using gallery title as alt text
                    'is_visible' => true
                ]);
            }
        }

        return redirect()->route('dashboard.galleries');
    }

    public function edit(Gallery $gallery)
    {
        return Inertia::render('Dashboard/Gallery/Edit', [
            'gallery' => $gallery->load('images'),
        ]);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required|in:news,event,infrastructure,nature',
            'description' => 'required',
            'images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'alts.*' => 'required_with:images.*|string|max:255',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:images,id'
        ]);

        // Update gallery details
        $gallery->update([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
        ]);

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
                    'alt' => $request->alts[$index],
                    'is_visible' => true
                ]);
            }
        }

        return redirect()->route('dashboard.galleries.index')
            ->with('success', 'Gallery updated successfully');
    }
}
