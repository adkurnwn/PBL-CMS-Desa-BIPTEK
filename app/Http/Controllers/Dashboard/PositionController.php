<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Position;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PositionController extends Controller
{
    public function create()
    {
        return Inertia::render('Dashboard/UserManagement/Positions/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'job_descriptions' => 'required|array|min:1',
        ]);

        Position::create([
            'name' => $request->name,
            'job_description' => json_encode($request->job_descriptions),
        ]);

        return redirect()->route('dashboard.users')->with('success', 'Position created successfully');
    }

    public function edit(Position $position)
    {
        return Inertia::render('Dashboard/UserManagement/Positions/Edit', [
            'position' => $position,
        ]);
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'job_descriptions' => 'required|array|min:1',
        ]);

        $position->update([
            'name' => $request->name,
            'job_description' => json_encode($request->job_descriptions),
        ]);

        return redirect()->route('dashboard.users')->with('success', 'Position updated successfully');
    }

    public function destroy(Position $position)
    {
        // Don't allow deleting core positions
        if (in_array($position->name, ['Admin Sistem', 'Kepala Desa'])) {
            return back()->with('error', 'Cannot delete core positions');
        }

        // Check if position is in use
        if ($position->users()->count() > 0) {
            return back()->with('error', 'Cannot delete position that is assigned to users');
        }

        $position->delete();

        return back()->with('success', 'Position deleted successfully');
    }
}
