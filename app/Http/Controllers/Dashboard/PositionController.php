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
            'name' => 'required|string|max:255|unique:positions,name',
            'job_descriptions' => 'required|array|min:1',
        ]);

        Position::create([
            'name' => $request->name,
            'job_description' => json_encode($request->job_descriptions),
        ]);

        $this->logHelper->logActivity('create', 'position', 'Menambahkan posisi ' . $request->name);
        return redirect()->route('dashboard.users.index')->with('success', 'Posisi Berhasil Ditambahkan');
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
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'job_descriptions' => 'required|array|min:1',
        ]);

        $position->update([
            'name' => $request->name,
            'job_description' => json_encode($request->job_descriptions),
        ]);

        $this->logHelper->logActivity('update', 'position', 'Meperbarui posisi ' . $position->name);
        return redirect()->route('dashboard.users.index')->with('success', 'Posisi Berhasil Diperbarui');
    }

    public function destroy(Position $position)
    {
        if (in_array($position->name, ['Admin Sistem', 'Kepala Desa'])) {
            return back()->with('error', 'Tidak dapat menghapus posisi ini');
        }

        if ($position->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus posisi yang sedang digunakan oleh pengguna');
        }

        $position->delete();

        $this->logHelper->logActivity('delete', 'position', 'Menghapus posisi ' . $position->name);
        return back()->with('success', 'Posisi Berhasil Dihapus');
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $perPage = $request->input('per_page', 25);
        
        $positionsQuery = Position::query();
        
        // Apply search if provided
        if (!empty($search)) {
            $positionsQuery->where('name', 'like', "%{$search}%")
                ->orWhere('job_description', 'like', "%{$search}%");
        }
        
        // Apply sorting
        $positionsQuery->orderBy($sort, $direction);
        
        // Get paginated results
        $positions = $positionsQuery->paginate($perPage)->withQueryString();
        
        return Inertia::render('Dashboard/UserManagement/Positions/Index', [
            'positions' => $positions,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }
}
