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
use App\Services\MediaService;

class UserManagementController extends Controller
{
    protected $logHelper;
    protected $mediaService;
    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        $this->logHelper = new LogActivityHelper();
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        $search = $request->input('search', '');
        $positionFilter = $request->input('position_id');
        $statusFilter = $request->has('status') ? (int)$request->input('status') : null;
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');

        $allowedSortFields = ['name', 'email', 'status', 'position_id', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }

        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        $filters = [];
        if ($positionFilter) {
            $filters['position_id'] = $positionFilter;
        }
        if ($statusFilter !== null) {
            $filters['status'] = $statusFilter;
        }

        $usersQuery = User::with(['roles', 'position'])
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Admin');
            });

        if (!empty($search)) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($positionFilter) {
            $usersQuery->where('position_id', $positionFilter);
        }

        if ($statusFilter !== null) {
            $usersQuery->where('status', $statusFilter);
        }

        $usersQuery->orderBy($sortField, $sortDirection);

        $users = $usersQuery->paginate($perPage);

        return Inertia::render('Dashboard/UserManagement/Index', [
            'users' => $users,
            'search' => $search,
            'filters' => $filters,
            'positions' => Position::all(),
            'sort' => $sortField,
            'direction' => $sortDirection
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

        $userData = $request->all();
        $userData['status'] = true;
        $userData['position_id'] = $position_id;
        $userData['password'] = Hash::make($request->password);

        if ($request->hasFile('photo_profile')) {
            $userData['photo_profile'] = $this->mediaService->upload(
                $request->file('photo_profile'),
                'profile-photos'
            );
        }

        $user = User::create($userData);

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

        $userData = $request->except(['password', 'photo_profile']);
        $userData['position_id'] = $position_id;

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo_profile')) {
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
        return redirect()->route('dashboard.users.index')->with('success', 'Pengguna berhasil diperbarui');
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
        return redirect()->route('dashboard.users.index')->with('success', 'Pengguna berhasil ' . $statusMessage);
    }
}
