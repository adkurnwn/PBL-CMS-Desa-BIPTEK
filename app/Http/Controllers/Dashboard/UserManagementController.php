<?php

namespace App\Http\Controllers\Dashboard;

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
        return Inertia::render('Dashboard/UserManagement/Index', [
            'users' => User::with('roles')->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Admin');
            })->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/UserManagement/Create');
    }

    public function store(Request $request)
    {
        if ($request->position === 'Kepala Desa') {
            $existingKepalaDesa = User::whereHas('roles', function ($query) {
                $query->where('name', 'Kepala Desa');
            })->where('status', true)->first();

            if ($existingKepalaDesa) {
                return back()->withErrors([
                    'position' => 'There is already an active Kepala Desa'
                ]);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', Rules\Password::defaults()],
            'phone' => 'required|string|max:20',
            'photo_profile' => 'nullable|image|max:1024|mimes:jpg,png,jpeg',
            'position' => 'required|string',
            'job_description' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'position' => $request->position,
            'job_description' => json_encode($request->job_description),
            'status' => $request->status,
        ];

        if ($request->hasFile('photo_profile')) {
            $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos');
        }

        $user = User::create($userData);

        if ($request->position === 'Kepala Desa') {
            $user->assignRole('Kepala Desa');
        } else {
            $user->assignRole('Aparat Desa');
        }

        return redirect()->route('dashboard.users')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return Inertia::render('Dashboard/UserManagement/Edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($request->position === 'Kepala Desa' && $request->status) {
            $existingKepalaDesa = User::whereHas('roles', function ($query) use ($user) {
                $query->where('name', 'Kepala Desa');
            })->where('status', true)->where('id', '!=', $user->id)->first();

            if ($existingKepalaDesa) {
                return back()->withErrors([
                    'position' => 'There is already an active Kepala Desa'
                ]);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'position' => 'required|string',
            'job_description' => 'nullable|array',
            'status' => 'required|boolean',
            'id_position' => 'nullable|exists:positions,id',
        ]);

        if ($request->filled('password')) {
            $rules['password'] = ['required', Rules\Password::defaults()];
        }

        if ($request->hasFile('photo_profile')) {
            $rules['photo_profile'] = 'image|max:1024|mimes:jpg,png,jpeg';
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'job_description' => json_encode($request->job_description),
            'status' => $request->status,
            'id_position' => $request->id_position,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo_profile')) {
            $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos');
        }

        $user->update($userData);

        if ($request->position === 'Kepala Desa') {
            $user->syncRoles('Kepala Desa');
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
