<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\KepalaDesa;
use App\Http\Resources\User\UserPreview;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with('position')->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Admin');
            })->where('status', true)->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active users found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'users' => UserPreview::collection($users),
                    'organization_image' => Profile::latest()->first()->organization_image ?? null,
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
