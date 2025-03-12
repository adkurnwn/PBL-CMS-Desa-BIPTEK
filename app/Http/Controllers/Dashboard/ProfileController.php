<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Profile/Index', [
            'profiles' => Profile::all()
        ]);
    }

    public function create()
    {
        if (Profile::exists()) {
            return redirect()->route('dashboard.profiles')
                ->with('error', 'Only one village profile can exist');
        }
        return Inertia::render('Dashboard/Profile/Create');
    }

    public function store(Request $request)
    {
        if (Profile::exists()) {
            return redirect()->route('dashboard.profiles')
                ->with('error', 'Only one village profile can exist');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:profiles',
            'phone' => 'required|string|max:255',
            'logo' => 'required|image|max:1024|mimes:jpg,png,jpeg',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'hero_image' => 'required|image|max:1024|mimes:jpg,png,jpeg',
            'organization_image' => 'required|image|max:1024|mimes:jpg,png,jpeg',
            'lembaga' => 'required|array',
            'fasilitas' => 'required|array',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'primary_color' => 'required|in:#000000,#ffffff',
            'secondary_color' => 'required|in:#000000,#ffffff'
        ]);

        $profileData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'lembaga' => json_encode($request->lembaga),
            'fasilitas' => json_encode($request->fasilitas),
            'history' => $request->history,
            'vision' => $request->vision,
            'mission' => $request->mission,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color
        ];

        // Handle file upload
        if ($request->hasFile('logo')) {
            $profileData['logo'] = $request->file('logo')->store('profile-logos', 'public');
        }
        if ($request->hasFile('organization_image')) {
            $profileData['organization_image'] = $request->file('organization_image')->store('organization-images', 'public');
        }
        if ($request->hasFile('hero_image')) {
            $profileData['hero_image'] = $request->file('hero_image')->store('hero-images', 'public');
        }

        Profile::create($profileData);

        return redirect()->route('dashboard.profiles')->with('success', 'Village profile created successfully');
    }

    public function edit(Profile $profile)
    {
        return Inertia::render('Dashboard/Profile/Edit', [
            'profile' => $profile
        ]);
    }

    public function update(Request $request, Profile $profile)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255',
            'phone' => 'required|string|max:255',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'lembaga' => 'required|array',
            'fasilitas' => 'required|array',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'primary_color' => 'required|in:#000000,#ffffff',
            'secondary_color' => 'required|in:#000000,#ffffff'
        ]);

        $profileData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'lembaga' => json_encode($request->lembaga),
            'fasilitas' => json_encode($request->fasilitas),
            'history' => $request->history,
            'vision' => $request->vision,
            'mission' => $request->mission,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color
        ];

        // Handle file upload
        if ($request->hasFile('logo')) {
            $profileData['logo'] = $request->file('logo')->store('profile-logos', 'public');
        }
        if ($request->hasFile('organization_image')) {
            $profileData['organization_image'] = $request->file('organization_image')->store('organization-images', 'public');
        }
        if ($request->hasFile('hero_image')) {
            $profileData['hero_image'] = $request->file('hero_image')->store('hero-images', 'public');
        }

        $profile->update($profileData);

        return redirect()->route('dashboard.profiles')->with('success', 'Village profile updated successfully');
    }
}
