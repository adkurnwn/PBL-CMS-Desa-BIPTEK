<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Departement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class DepartementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/DepartementManagement/Index', [
            'departements' => Departement::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DepartementManagement/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:' . Departement::class . ',id',
        ]);

        Departement::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('admin.departements')->with('success', 'Departement created successfully');
    }

    public function edit(Departement $departement)
    {
        return Inertia::render('Admin/DepartementManagement/Edit', [
            'user' => $departement
        ]);
    }

    public function update(Request $request, Departement $departement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:' . Departement::class . ',id',
        ]);

        $departement->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('admin.departements')->with('success', 'Departement updated successfully');
    }

    public function destroy(Departement $departement)
    {
        $departement->delete();
        return redirect()->route('admin.departements')->with('success', 'Departement deleted successfully');
    }
}
