<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

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
        $permissions = Permission::all();

        // Create a map of role_id => [permission_names]
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->id] = $role->permissions->pluck('name')->toArray();
        }

        return Inertia::render('Dashboard/RoleManagement/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function edit(Role $role)
    {
        if ($role->name === 'admin') {
            abort(403, 'Cannot edit admin role');
        }

        return Inertia::render('Dashboard/RoleManagement/Edit', [
            'role' => $role,
            'permissions' => Permission::all(),
            'rolePermissions' => $role->permissions->pluck('name')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            abort(403, 'Cannot modify admin role permissions');
        }

        $request->validate([
            'permissions' => 'required|array'
        ]);

        $role->syncPermissions($request->permissions);

        $this->logHelper->logActivity('update', 'role', 'Memperbarui hak akses role ' . $role->name);
        return redirect()->back()->with('success', 'Hak akses role berhasil diperbarui');
    }

    public function batchUpdate(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array'
        ]);

        foreach ($request->permissions as $roleId => $permissionNames) {
            $role = Role::find($roleId);

            // Skip admin role for security
            if ($role->name === 'admin') {
                continue;
            }

            $role->syncPermissions($permissionNames);
        }

        $this->logHelper->logActivity('update', 'role', 'Memperbarui hak akses beberapa role sekaligus');
        return redirect()->back()->with('success', 'Hak akses role berhasil diperbarui');
    }
}
