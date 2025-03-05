<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class PositionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/PositionManagement/Index', [
            'positions' => Position::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PositionManagement/Create', [
            'positions' => Position::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:' . Position::class . ',id',
        ]);

        Position::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('admin.positions')->with('success', 'Position created successfully');
    }

    public function edit(Position $position)
    {
        return Inertia::render('Admin/PositionManagement/Edit', [
            'position' => $position,
            'positions' => Position::where('id', '!=', $position->id)->get()
        ]);
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:' . Position::class . ',id',
        ]);

        $position->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('admin.positions')->with('success', 'Position updated successfully');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('admin.positions')->with('success', 'Position deleted successfully');
    }
}
