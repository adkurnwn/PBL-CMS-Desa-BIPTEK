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

    public function landingPage()
    {
        try {
            $data = Cache::remember('profile.landing', 1200, function () {
                $profile = Profile::latest()->first();

                if (!$profile) {
                    return null;
                }

                return [
                    'profile' => $profile,
                    'produk_hukum' => ProdukHukum::exists(),
                    'dana_desa' => DanaDesa::exists(),
                ];
            });

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $data['profile']->name,
                    'hero_image' => json_decode($data['profile']->hero_images)[0],
                    'produk_hukum' => $data['produk_hukum'],
                    'dana_desa' => $data['dana_desa'],
                ]
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

    public function layananPage()
    {
        try {
            $data = Cache::remember('profile.layanan', 1200, function () {
                $profile = Profile::latest()->first();

                if (!$profile) {
                    return null;
                }

                return [
                    'profile' => $profile,
                    'produk_hukum' => ProdukHukum::exists(),
                    'dana_desa' => DanaDesa::exists(),
                    'demografi' => Demografi::exists(),
                ];
            });

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $data['profile']->name,
                    'produk_hukum' => $data['produk_hukum'],
                    'dana_desa' => $data['dana_desa'],
                    'demografi' => $data['demografi'],
                ]
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
