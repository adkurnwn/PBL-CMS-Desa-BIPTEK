<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/UserManagement/Index', [
            'users' => User::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/UserManagement/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', Rules\Password::defaults()],
            'phone' => 'required|string|max:20',
            'photo_profile' => 'nullable|image|max:1024|mimes:jpg,png,jpeg',
            'position' => 'required|string',
            'job_description' => 'nullable|array',
            'status' => 'required|boolean',
            'role' => 'required|string|exists:roles,name',
        ]);

        // Convert job_description array to JSON before saving
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'position' => $request->position,
            'job_description' => json_encode($request->job_description), // Encode array as JSON
            'status' => $request->status,
        ];

        // Handle file upload separately
        if ($request->hasFile('photo_profile')) {
            $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos');
        }

        $user = User::create($userData);

        $user->assignRole($request->role);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/UserManagement/Edit', [
            'user' => array_merge($user->toArray(), [
                'role' => $user->roles->first()->name ?? null
            ]),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'position' => 'required|string',
            'job_description' => 'nullable|array',
            'status' => 'required|boolean',
            'role' => 'required|string|exists:roles,name',
            'id_position' => 'nullable|exists:positions,id',
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = ['required', Rules\Password::defaults()];
        }

        // Only validate photo if it's provided
        if ($request->hasFile('photo_profile')) {
            $rules['photo_profile'] = 'image|max:1024|mimes:jpg,png,jpeg';
        }

        $request->validate($rules);

        // Build the data array with only fields that should be updated
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'job_description' => json_encode($request->job_description),
            'status' => $request->status,
            'id_position' => $request->id_position,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Only update photo if provided
        if ($request->hasFile('photo_profile')) {
            $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos');
        }

        $user->update($userData);

        // Update role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}
