<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/RoleManagement/Index', [
            'roles' => Role::all()
        ]);
    }

    public function edit(Role $role)
    {
        return Inertia::render('Admin/RoleManagement/Edit', [
            'role' => $role,
            'permissions' => Permission::all(),
            'rolePermissions' => $role->permissions->pluck('name')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array'
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Role permissions updated successfully');
    }
}
