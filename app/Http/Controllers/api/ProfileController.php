<?php

namespace App\Http\Controllers\api;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Profile\ProfileDetail;
use App\Http\Resources\Profile\ProfileLayout;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function layout()
    {
        try {
            $profile = Profile::with(['province', 'regency', 'district'])->first();

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
            $profile = Profile::latest()->first();

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $profile->name,
                    'hero_image' => json_decode($profile->hero_images)[0],
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
            $profile = Profile::latest()->first();

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

    public function getName()
    {
        try {
            $profile = Profile::latest()->first();

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $profile->name,
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
