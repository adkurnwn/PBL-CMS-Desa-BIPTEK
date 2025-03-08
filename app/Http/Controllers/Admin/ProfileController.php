<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Profile/Index', [
            'profiles' => Profile::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Profile/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:profiles',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'required|image|max:1024|mimes:jpg,png,jpeg',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string'
        ]);

        // Handle file upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('profile-logos', 'public');
        }

        Profile::create($validated);

        return redirect()->route('admin.profiles')->with('success', 'Village profile created successfully');
    }
}
