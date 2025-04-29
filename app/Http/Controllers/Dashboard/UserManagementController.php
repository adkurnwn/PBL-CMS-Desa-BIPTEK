<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validation\StoreUserManagementRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validation\UpdateUserManagementRequest;

class UserManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/UserManagement/Index', [
            'users' => User::with(['roles', 'position'])->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Admin');
            })->get(),
            'positions' => Position::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/UserManagement/Create', [
            'positions' => Position::all()
        ]);
    }

    public function store(StoreUserManagementRequest $request)
    {
        if ($request->filled('new_position')) {
            $position = Position::create([
                'name' => $request->new_position,
                'job_description' => json_encode($request->job_descriptions)
            ]);
            $position_id = $position->id;
        } else {
            $position = Position::findOrFail($request->position_id);
            $position_id = $position->id;
        }

        if ($position->name === 'Kepala Desa') {
            $existingKepalaDesa = User::whereHas('roles', function ($query) {
                $query->where('name', 'Kepala Desa');
            })->where('status', true)->first();

            if ($existingKepalaDesa) {
                return back()->withErrors([
                    'position_id' => 'There is already an active Kepala Desa'
                ]);
            }
        }

        // Create user
        $userData = $request->all();
        $userData['status'] = true;
        $userData['position_id'] = $position_id;
        $userData['password'] = Hash::make($request->password);

        if ($request->hasFile('photo_profile')) {
            $path = $request->file('photo_profile')->store('profile-photos', 'public');
            $userData['photo_profile'] = $path;
        }

        $user = User::create($userData);

        // Assign role based on position name
        if ($position->name === 'Kepala Desa') {
            $user->assignRole('Kepala Desa');
        } else if ($position->name === 'Admin Sistem') {
            $user->assignRole('Admin');
        } else {
            $user->assignRole('Aparat Desa');
        }

        return redirect()->route('dashboard.users')->with('success', 'User Added Successfully');
    }

    public function edit(User $user)
    {
        return Inertia::render('Dashboard/UserManagement/Edit', [
            'user' => $user->load('position'),
            'positions' => Position::all()
        ]);
    }

    public function update(UpdateUserManagementRequest $request, User $user)
    {
        if ($request->filled('new_position')) {
            $position = Position::create([
                'name' => $request->new_position,
                'job_description' => json_encode($request->job_descriptions)
            ]);
            $position_id = $position->id;
        } else {
            $position = Position::findOrFail($request->position_id);
            $position_id = $position->id;
        }

        if ($position->name === 'Kepala Desa' && $request->status) {
            $existingKepalaDesa = User::whereHas('roles', function ($query) use ($user) {
                $query->where('name', 'Kepala Desa');
            })->where('status', true)->where('id', '!=', $user->id)->first();

            if ($existingKepalaDesa) {
                return back()->withErrors([
                    'position_id' => 'There is already an active Kepala Desa'
                ]);
            }
        }

        // Get all fields except password and photo_profile
        $userData = $request->except(['password', 'photo_profile']);
        $userData['position_id'] = $position_id;

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo_profile')) {
            // Delete the old photo if it exists
            if ($user->photo_profile) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos', 'public');
        }

        $user->update($userData);

        if ($position->name === 'Kepala Desa') {
            $user->syncRoles('Kepala Desa');
        } else if ($position->name === 'Admin Sistem') {
            $user->syncRoles('Admin');
        } else {
            $user->syncRoles(['Aparat Desa']);
        }

        return redirect()->route('dashboard.users')->with('success', 'User Updated Successfully');
    }

    public function destroy(User $user)
    {
        if ($user->status === false) {
            $user->update([
                'status' => true
            ]);
        } else {
            $user->update([
                'status' => false
            ]);
        }

        return redirect()->route('dashboard.users')->with('success', 'User Deleted Successfully');
    }
}
