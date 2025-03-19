<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Region\Regency;
use App\Models\Region\Village;
use App\Models\Region\District;
use App\Models\Region\Province;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    public function getProvinces(): JsonResponse
    {
        return response()->json(Province::all());
    }

    public function getRegencies($province_id): JsonResponse
    {
        return response()->json(Regency::where('province_id', $province_id)->get());
    }

    public function getDistricts($regency_id): JsonResponse
    {
        return response()->json(District::where('regency_id', $regency_id)->get());
    }

    public function getVillages($district_id): JsonResponse
    {
        return response()->json(Village::where('district_id', $district_id)->get());
    }
}
