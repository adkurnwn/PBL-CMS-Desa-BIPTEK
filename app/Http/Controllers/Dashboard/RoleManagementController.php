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
        return Inertia::render('Dashboard/RoleManagement/Index', [
            'roles' => Role::where('name', '!=', 'Admin')->get()
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
}
