<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Category;
use App\Helpers\SlugHelper;
use App\Models\ProdukHukum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukHukumController extends Controller
{
    public function index()
    {
        return inertia('Dashboard/ProdukHukum/Index', [
            'produkHukum' => ProdukHukum::with('category')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/ProdukHukum/Create', [
            'categories' => Category::where('category_type', 0)->get()
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'file' => 'required|file|mimes:pdf,doc,docx',
            'link' => 'required',
            'date' => 'required',
            'is_moderate' => 'sometimes|boolean',
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

        $request->validate($rules);

        // Handle category creation/editing
        if ($request->filled('category.new_category')) {
            $category = Category::create([
                'name' => $request->category['new_category'],
                'category_type' => 0
            ]);
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

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('produk-hukum-documents', 'public');
        }

        $data = $request->all();
        $data['slug'] = SlugHelper::generateUniqueSlug(ProdukHukum::class, $request->title);
        $data['user_id'] = Auth::id();
        $data['category_id'] = $category_id;
        $data['file'] = $path ?? null;

        $produkHukum = ProdukHukum::create($data);

        return redirect()->route('dashboard.produk-hukum')->with('success', 'Produk Hukum Added Successfully');
    }

    public function edit(ProdukHukum $produkHukum)
    {
        // Get all categories and their usage count
        $categories = Category::where('category_type', 0)->get();

        // Add usage count to each category
        foreach ($categories as $category) {
            $usageCount = ProdukHukum::where('category_id', $category->id)->count();
            $category->usage_count = $usageCount;
        }

        return Inertia::render('Dashboard/ProdukHukum/Edit', [
            'produkHukum' => $produkHukum->load('category'),
            'categories' => $categories,
            'unused_categories' => $categories->filter(function ($category) {
                return $category->usage_count === 0;
            })->values()
        ]);
    }

    public function update(Request $request, ProdukHukum $produkHukum)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'file' => 'nullable|file|mimes:pdf,doc,docx',
            'link' => 'required',
            'date' => 'required',
            'is_moderate' => 'sometimes|boolean',
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
            $category = Category::create([
                'name' => $request->category['new_category'],
                'category_type' => 0
            ]);
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
                    // Check if the category is being used by any produk hukum
                    $inUseCount = ProdukHukum::where('category_id', $category->id)->count();

                    if ($inUseCount > 0) {
                        // Skip deletion and add to results
                        $deletionResults[$category->name] = "Cannot delete: in use by {$inUseCount} produk hukum";
                    } else {
                        $category->delete();
                        $deletionResults[$category->name] = "Deleted successfully";
                    }
                }
            }

            // Add deletion results to the session for displaying feedback
            session()->flash('categoryDeletionResults', $deletionResults);
        }

        $data = $request->all();

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($produkHukum->file) {
                Storage::disk('public')->delete($produkHukum->file);
            }
            $path = $request->file('file')->store('produk-hukum-documents', 'public');
            $data['file'] = $path;
        } else {
            // Keep existing file
            unset($data['file']);
        }

        $data['slug'] = SlugHelper::generateUniqueSlug(ProdukHukum::class, $request->title);
        $data['category_id'] = $category_id;

        $produkHukum->update($data);

        return redirect()->route('dashboard.produk-hukum')->with('success', 'Produk Hukum Updated Successfully');
    }

    public function destroy(ProdukHukum $produkHukum)
    {
        // Delete associated file
        if ($produkHukum->file) {
            Storage::disk('public')->delete($produkHukum->file);
        }

        $produkHukum->delete();

        return redirect()->route('dashboard.produk-hukum')->with('success', 'Produk Hukum Deleted Successfully');
    }
}
