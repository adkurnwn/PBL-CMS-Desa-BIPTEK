<?php

namespace App\Http\Controllers\api;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Profile\ProfileDetail;
use App\Http\Resources\Profile\ProfileLayout;
use App\Models\DanaDesa;
use App\Models\Demografi;
use App\Models\ProdukHukum;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function layout()
    {
        try {
            $profile = Cache::remember('profile.layout', 1200, function () {
                return Profile::with(['province', 'regency', 'district'])->first();
            });

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProfileLayout($profile)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profilePage()
    {
        try {
            $profile = Cache::remember('profile.detail', 1200, function () {
                return Profile::latest()->first();
            });

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProfileDetail($profile)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
