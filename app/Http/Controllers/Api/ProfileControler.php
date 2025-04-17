<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileControler extends Controller
{
    public function index()
    {
        $profile = Profile::first();
        return response()->json($profile);
    }

    public function navbar()
    {
        $profile = Profile::first();
        return response()->json([
            'navbar' => [
                'logo' => $profile->logo,
                'name' => $profile->name,
                'kabupaten' => $profile->kabupaten,
            ]
        ]);
    }
}
