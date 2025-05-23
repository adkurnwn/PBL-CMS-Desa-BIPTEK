<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
        $users = User::with(['position'])
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Admin');
            })
            ->where('status', true) // Only active users
            ->get();
        $profile = Profile::first();
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json([
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo_profile' => $user->photo_profile ? asset('storage/' . $user->photo_profile) : null,
                    'position' => $user->position ? [
                        'id' => $user->position->id,
                        'name' => $user->position->name
                    ] : null
                ];
            }),
            'organization_image' => $profile->organization_image ? asset('storage/' . $profile->organization_image) : null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
