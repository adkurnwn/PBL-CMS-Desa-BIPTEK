<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Position;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/UserManagement/Index', [
            'users' => User::with(['roles', 'position'])->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Admin');
            })->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/UserManagement/Create', [
            'positions' => Position::all()
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', Rules\Password::defaults()],
            'phone' => 'required|string|max:20',
            'photo_profile' => 'nullable|image|max:1024|mimes:jpg,png,jpeg',
            'status' => 'required|boolean',
        ];

        if ($request->filled('new_position')) {
            $rules['new_position'] = 'required|string|max:255';
            $rules['job_descriptions'] = 'required|array|min:1';
        } else {
            $rules['position_id'] = 'required|exists:positions,id';
        }

        $request->validate($rules);

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
        $userData['position_id'] = $position_id;
        $userData['password'] = Hash::make($request->password);

        if ($request->hasFile('photo_profile')) {
            $path = $request->file('photo_profile')->store('profile-photos', 'public');
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

        return redirect()->route('dashboard.users')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return Inertia::render('Dashboard/UserManagement/Edit', [
            'user' => $user->load('position'),
            'positions' => Position::all()
        ]);
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'status' => 'required|boolean',
        ];

        // Validate position fields based on whether it's new or existing
        if ($request->filled('new_position')) {
            $rules['new_position'] = 'required|string|max:255';
            $rules['job_descriptions'] = 'required|array|min:1';
        } else {
            $rules['position_id'] = 'required|exists:positions,id';
        }

        if ($request->filled('password')) {
            $rules['password'] = ['required', Rules\Password::defaults()];
        }

        if ($request->hasFile('photo_profile')) {
            $rules['photo_profile'] = 'image|max:1024|mimes:jpg,png,jpeg';
        }

        $request->validate($rules);

        // Handle position (create new if needed)
        if ($request->filled('new_position')) {
            // Create a new position
            $position = Position::create([
                'name' => $request->new_position,
                'job_description' => json_encode($request->job_descriptions)
            ]);
            $position_id = $position->id;
        } else {
            $position = Position::findOrFail($request->position_id);
            $position_id = $position->id;
        }

        // Check if trying to update to Kepala Desa when one already exists
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

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position_id' => $position_id,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo_profile')) {
            $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos', 'public');
        }

        $user->update($userData);

        // Update role based on position name
        if ($position->name === 'Kepala Desa') {
            $user->syncRoles('Kepala Desa');
        } else if ($position->name === 'Admin Sistem') {
            $user->syncRoles('Admin');
        } else {
            $user->syncRoles(['Aparat Desa']);
        }

        return redirect()->route('dashboard.users')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('dashboard.users')->with('success', 'User deleted successfully');
    }
}
