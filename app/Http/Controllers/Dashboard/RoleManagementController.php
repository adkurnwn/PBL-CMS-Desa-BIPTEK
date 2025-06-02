<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class RoleManagementController extends Controller
{
    protected $logHelper;

    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

    public function index()
    {
        $roles = Role::where('name', '!=', 'Admin')->get();

        return Inertia::render('Dashboard/RoleManagement/Index', [
            'roles' => $roles
        ]);
    }

    public function edit(Role $role)
    {
        if (strtolower($role->name) === 'admin') {
            abort(403, 'Cannot edit admin role');
        }

        return Inertia::render('Dashboard/RoleManagement/Edit', [
            'role' => $role,
            'permissions' => Permission::all(),
            'rolePermissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }

    public function update(Request $request, Role $role)
    {
        Log::info('Role update request received', [
            'role' => $role->name,
            'permissions' => $request->permissions
        ]);

        if (strtolower($role->name) === 'admin') {
            abort(403, 'Cannot modify admin role permissions');
        }

        $validated = $request->validate([
            'permissions' => 'present|array',
            'permissions.*' => 'string|exists:permissions,name'
        ], [
            'permissions.present' => 'Daftar hak akses wajib diberikan.',
            'permissions.array' => 'Format data hak akses tidak valid.',
            'permissions.*.exists' => 'Salah satu hak akses yang diberikan tidak valid.'
        ]);

        $permissions = $request->permissions ?? [];

        Log::info('Before syncing permissions', [
            'role' => $role->name,
            'current_permissions' => $role->permissions->pluck('name'),
            'new_permissions' => $permissions
        ]);

        $role->syncPermissions($permissions);

        Log::info('After syncing permissions', [
            'role' => $role->name,
            'new_permissions' => $role->permissions->pluck('name')
        ]);

        $this->logHelper->logActivity('update', 'role', 'Memperbarui hak akses role ' . $role->name);
        return redirect()->back()->with('success', 'Hak Akses Role berhasil diperbarui');
    }

    public function batchUpdate(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array'
        ]);

        foreach ($request->permissions as $roleId => $permissionNames) {
            $role = Role::find($roleId);

            if (strtolower($role->name) === 'admin') {
                continue;
            }

            $role->syncPermissions($permissionNames);
        }

        $this->logHelper->logActivity('update', 'role', 'Memperbarui hak akses beberapa role sekaligus');
        return redirect()->back()->with('success', 'Hak Akses Role berhasil diperbarui');
    }
}
