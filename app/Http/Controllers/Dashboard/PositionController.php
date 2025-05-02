<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;

class PositionController extends Controller
{
    protected $logHelper;
    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

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

        $this->logHelper->logActivity('create', 'position', 'Menambahkan posisi ' . $request->name);
        return redirect()->route('dashboard.users')->with('success', 'Posisi Berhasil Ditambahkan');
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

        $this->logHelper->logActivity('update', 'position', 'Meperbarui posisi ' . $position->name);
        return redirect()->route('dashboard.users')->with('success', 'Posisi Berhasil Diperbarui');
    }

    public function destroy(Position $position)
    {
        if (in_array($position->name, ['Admin Sistem', 'Kepala Desa'])) {
            return back()->with('error', 'Cannot delete core positions');
        }

        if ($position->users()->count() > 0) {
            return back()->with('error', 'Cannot delete position that is assigned to users');
        }

        $position->delete();

        $this->logHelper->logActivity('delete', 'position', 'Menghapus posisi ' . $position->name);
        return back()->with('success', 'Posisi Berhasil Dihapus');
    }
}
