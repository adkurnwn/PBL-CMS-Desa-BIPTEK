<?php

namespace App\Http\Controllers\api;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Region\Regency;
use App\Http\Controllers\Controller;
use App\Models\Region\District;
use App\Models\Region\Province;

class LayoutController extends Controller
{
    public function index()
    {
        $profile = Profile::first();
        $regency = Regency::find($profile->kabupaten);
        $district = District::find($profile->kecamatan);
        $province = Province::find($profile->provinsi);

        return response()->json([
            'logo' => $profile->logo,
            'name' => $profile->name,
            'kecamatan' => ucwords(strtolower($district->name)),
            'kabupaten' => ucwords(strtolower($regency->name)),
            'provinsi' => ucwords(strtolower($province->name)),
            'phone' => $profile->phone,
            'email' => $profile->email,
        ]);
    }
}
