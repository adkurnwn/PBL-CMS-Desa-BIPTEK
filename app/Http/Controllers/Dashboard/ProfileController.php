<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Profile;
use App\Models\Region\Village;
use App\Services\MediaService;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validation\UpdateProfileRequest;

class ProfileController extends Controller
{
    protected $mediaService;
    protected $logHelper;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        $this->logHelper = new LogActivityHelper();
    }

    public function index()
    {
        return Inertia::render('Dashboard/Profile/Index', [
            'profiles' => Profile::all()
        ]);
    }

    public function edit($slug)
    {
        $profile = Profile::where('name', $slug)->firstOrFail();

        return Inertia::render('Dashboard/Profile/Edit', [
            'profile' => $profile
        ]);
    }

    public function update(UpdateProfileRequest $request, $slug)
    {
        $profile = Profile::where('name', $slug)->firstOrFail();

        $profileData = $request->except([
            'logo',
            'organization_image',
            'hero_images',
            'hero_image_1',
            'hero_image_2',
            'hero_image_3',
            'lembaga',
            'fasilitas'
        ]);

        // Get the village name for the profile name
        $village = Village::find($request->kelurahan);
        if ($village) {
            $profileData['name'] = "Desa " . ucwords(strtolower($village->name));
        }

        // Handle JSON fields
        $profileData['lembaga'] = json_encode($request->lembaga);
        $profileData['fasilitas'] = json_encode($request->fasilitas);

        // Handle single uploads
        if ($request->hasFile('logo')) {
            if ($profile->logo) $this->mediaService->delete($profile->logo);
            $logoPath = $this->mediaService->upload($request->file('logo'), 'profile-logo');
            $profileData['logo'] = $logoPath;

            $this->updateFaviconFromLogo($logoPath);
        }

        if ($request->hasFile('organization_image')) {
            if ($profile->organization_image) $this->mediaService->delete($profile->organization_image);
            $profileData['organization_image'] = $this->mediaService->upload($request->file('organization_image'), 'organization-images');
        }

        // Handle hero images
        if ($request->hasFile('hero_images')) {
            $this->mediaService->handleJsonImages($profile, $request->file('hero_images'), 'hero_images', 'hero-images');
        } else {
            $heroImageFiles = [];

            for ($i = 1; $i <= 3; $i++) {
                if ($request->hasFile("hero_image_$i")) {
                    $heroImageFiles[$i - 1] = $request->file("hero_image_$i");
                }
            }

            if (!empty($heroImageFiles)) {
                $this->mediaService->handleJsonImages($profile, $heroImageFiles, 'hero_images', 'hero-images');
            }
        }

        $profile->update($profileData);

        $this->logHelper->logActivity('update', 'profile', 'Memperbarui profil ' . $profile->name);
        return redirect()->route('dashboard.profiles.index')->with('success', 'Profil Berhasil Diperbarui');
    }

    protected function updateFaviconFromLogo(string $logoPath)
    {
        Storage::disk('public')->put('favicon.png', Storage::disk('public')->get($logoPath));
    }
}
