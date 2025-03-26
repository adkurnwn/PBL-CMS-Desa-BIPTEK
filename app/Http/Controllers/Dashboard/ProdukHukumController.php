<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ProdukHukum;
use Illuminate\Http\Request;
use App\Models\ProdukHukumCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukHukumController extends Controller
{
    public function index()
    {
        return inertia('Dashboard/ProdukHukum/Index', [
            'produkHukum' => ProdukHukum::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/ProdukHukum/Create', [
            'categories' => ProdukHukumCategory::all()
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

        ];

        if ($request->filled('category.new_category')) {
            $rules['category.new_category'] = 'required|string|max:255';
        } else {
            $rules['category.category_id'] = 'required|exists:produk_hukum_categories,id';
        }

        $request->validate($rules);

        if ($request->filled('category.new_category')) {
            $category = ProdukHukumCategory::create(['name' => $request->category['new_category']]);
            $category_id = $category->id;
        } else {
            $category_id = $request->category['category_id'];
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('produk-hukum-documents', 'public');
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['produk_hukum_category_id'] = $category_id;
        $data['file'] = $path;


        $produkHukum = ProdukHukum::create($data);

        return redirect()->route('dashboard.produk-hukum');
    }

    public function edit(ProdukHukum $produkHukum)
    {
        return Inertia::render('Dashboard/ProdukHukum/Edit', [
            'produkHukum' => $produkHukum->load('category'),
            'categories' => ProdukHukumCategory::all()
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
        ];

        if ($request->filled('category.new_category')) {
            $rules['category.new_category'] = 'required|string|max:255';
        } else {
            $rules['category.category_id'] = 'required|exists:produk_hukum_categories,id';
        }

        $request->validate($rules);

        if ($request->filled('category.new_category')) {
            $category = ProdukHukumCategory::create(['name' => $request->category['new_category']]);
            $category_id = $category->id;
        } else {
            $category_id = $request->category['category_id'];
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


        $data['produk_hukum_category_id'] = $category_id;

        $produkHukum->update($data);

        return redirect()->route('dashboard.produk-hukum');
    }
}
