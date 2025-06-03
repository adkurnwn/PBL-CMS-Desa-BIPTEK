<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Demografi;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Validation\StoreDemografiRequest;
use App\Http\Requests\Validation\UpdateDemografiRequest;

class DemografiController extends Controller
{
    protected $logHelper;

    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

    public function index(Request $request)
    {
        $query = Demografi::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tahun', 'like', "%{$search}%")
                    ->orWhere('jumlah_penduduk', 'like', "%{$search}%")
                    ->orWhere('jumlah_keluarga', 'like', "%{$search}%")
                    ->orWhere('jumlah_pendatang', 'like', "%{$search}%")
                    ->orWhere('jumlah_pindah_keluar', 'like', "%{$search}%");
            });
        }

        if ($request->has('tahun') && $request->tahun !== null && $request->tahun !== '') {
            $query->where('tahun', $request->tahun);
        }

        $sortField = $request->input('sort', 'tahun');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $years = Demografi::distinct()->pluck('tahun')->toArray();
        $yearsFormatted = array_map(function ($year) {
            return ['id' => $year, 'name' => (string)$year];
        }, $years);

        return Inertia::render('Dashboard/Demografi/Index', [
            'demografi' => $query->get(),
            'search' => $request->search,
            'sort' => $sortField,
            'direction' => $sortDirection,
            'filters' => [
                'tahun' => $request->input('tahun', ''),
            ],
            'years' => $yearsFormatted,
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Demografi/Create');
    }

    public function store(StoreDemografiRequest $request)
    {
        $demografiData = $request->all();
        $demografiData['user_id'] = Auth::id();

        Demografi::create($demografiData);

        $this->logHelper->logActivity('create', 'demografi', 'Menambahkan demografi ' . $request->title);
        return redirect()->route('dashboard.demografi.index')->with('success', 'Data demografi Berhasil Ditambahkan');
    }

    public function edit($slug)
    {
        $demografi = Demografi::where('tahun', $slug)->firstOrFail();
        return Inertia::render('Dashboard/Demografi/Edit', [
            'demografi' => $demografi,
        ]);
    }

    public function update(UpdateDemografiRequest $request, $slug)
    {
        $demografi = Demografi::where('tahun', $slug)->firstOrFail();

        if ($request->tahun != $slug) {
            $existingDemografi = Demografi::where('tahun', $request->tahun)->first();

            if ($existingDemografi) {
                return redirect()->back()->withErrors([
                    'tahun' => 'Data demografi untuk tahun ini sudah ada.'
                ])->withInput();
            }
        }

        $demografiData = $request->all();
        $demografi->update($demografiData);

        $this->logHelper->logActivity('update', 'demografi', 'Memperbarui demografi ' . $request->title);
        return redirect()->route('dashboard.demografi.index')->with('success', 'Data demografi Berhasil Diperbarui');
    }
}
