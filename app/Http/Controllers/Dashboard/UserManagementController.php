<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validation\StoreUserManagementRequest;
use App\Http\Requests\Validation\UpdateUserManagementRequest;

class UserManagementController extends Controller
{
    protected $logHelper;
    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }
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
        $position = Position::findOrFail($request->position_id);
        $position_id = $position->id;

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

        $this->logHelper->logActivity('create', 'user', 'Menambahkan pengguna ' . $user->name);
        return redirect()->route('dashboard.users.index')->with('success', 'Pengguna Berhasil Ditambahkan');
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
        $position = Position::findOrFail($request->position_id);
        $position_id = $position->id;

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

        $this->logHelper->logActivity('update', 'user', 'Memperbarui pengguna ' . $user->name);
        return redirect()->route('dashboard.users.index')->with('success', 'Pengguna Berhasil Diperbarui');
    }

    public function destroy(User $user)
    {
        $newStatus = !$user->status;
        $user->update([
            'status' => $newStatus
        ]);
        $logMessage = $newStatus ? 'Mengaktifkan ' : 'Menonaktifkan ';
        $statusMessage = $newStatus ? 'Diaktifkan' : 'Dinonaktifkan';
        $logStatus = $newStatus ? 'restore' : 'delete';
        $this->logHelper->logActivity($logStatus, 'user', $logMessage . 'pengguna ' . $user->name);
        return redirect()->route('dashboard.users.index')->with('success', 'Pengguna Berhasil ' . $statusMessage);
    }
}
