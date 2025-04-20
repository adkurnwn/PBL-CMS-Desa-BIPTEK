<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfilePageController extends Controller
{
    public function index()
    {
        $profile = Profile::first();
        $kepalaDesa = User::whereHas('roles', function ($query) {
            $query->where('name', 'Kepala Desa');
        })->where('status', true)->first();

        return response()->json([
            'hero_image' => json_decode($profile->hero_images)[1],
            'history' => $profile->history,
            'vision' => $profile->vision,
            'mission' => $profile->mission,
            'fasilitas' => $profile->fasilitas,
            'lembaga' => $profile->lembaga,
            'organization_image' => $profile->organization_image,
            'kepala_desa' => [
                'name' => $kepalaDesa->name,
                'photo_profile' => $kepalaDesa->photo_profile,
            ]
        ]);
    }
}
