<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Region\Village; // Add this at the top

class ProfileController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Profile/Index', [
            'profiles' => Profile::all()
        ]);
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
            'email' => 'required|string|lowercase|email|max:255',
            'phone' => 'required|string|max:255',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'address' => 'required|string',
            'lembaga' => 'required|array',
            'fasilitas' => 'required|array',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'primary_color' => 'required|in:#000000,#ffffff',
            'secondary_color' => 'required|in:#000000,#ffffff',
            'hero_images.*' => 'sometimes|image|max:2048|mimes:jpg,png,jpeg',
            'hero_image_1' => 'nullable|image|max:2048|mimes:jpg,png,jpeg',
            'hero_image_2' => 'nullable|image|max:2048|mimes:jpg,png,jpeg',
            'hero_image_3' => 'nullable|image|max:2048|mimes:jpg,png,jpeg',
        ]);

        $village = Village::find($request->kelurahan);
        if ($village) {
            $formattedName = ucwords(strtolower($village->name));
            $villageName = "Desa " . $formattedName;
        }

        $profileData = [
            'name' => $villageName,
            'email' => $request->email,
            'phone' => $request->phone,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'address' => $request->address,
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
            $profileData['logo'] = $request->file('logo')->store('profile-logo', 'public');
        }
        if ($request->hasFile('organization_image')) {
            $profileData['organization_image'] = $request->file('organization_image')->store('organization-images', 'public');
        }
        if ($request->hasFile('hero_images')) {
            $heroImages = [];
            foreach ($request->file('hero_images') as $image) {
                $path = $image->store('hero-images', 'public');
                $heroImages[] = $path;
            }
            $profileData['hero_images'] = json_encode($heroImages);
        }

        $heroImages = json_decode($profile->hero_images) ?? [];

        for ($i = 1; $i <= 3; $i++) {
            if ($request->hasFile("hero_image_$i")) {
                $path = $request->file("hero_image_$i")->store('hero-images', 'public');
                $heroImages[$i - 1] = $path;
            }
        }

        $profileData['hero_images'] = json_encode(array_values($heroImages));

        $profile->update($profileData);

        return redirect()->route('dashboard.profiles')->with('success', 'Village profile updated successfully');
    }
}
