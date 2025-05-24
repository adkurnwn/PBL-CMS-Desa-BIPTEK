<?php

namespace App\Http\Controllers\api;

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
        try {
            $provinces = Province::all();
            if ($provinces->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No provinces found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $provinces
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve provinces'
            ], 500);
        }
    }

    public function getRegencies($province_id): JsonResponse
    {
        try {
            $regencies = Regency::where('province_id', $province_id)->get();
            if ($regencies->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No regencies found for this province'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $regencies
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve regencies'
            ], 500);
        }
    }

    public function getDistricts($regency_id): JsonResponse
    {
        try {
            $districts = District::where('regency_id', $regency_id)->get();
            if ($districts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No districts found for this regency'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $districts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve districts'
            ], 500);
        }
    }

    public function getVillages($district_id): JsonResponse
    {
        try {
            $villages = Village::where('district_id', $district_id)->get();
            if ($villages->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No villages found for this district'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $villages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve villages'
            ], 500);
        }
    }
}
