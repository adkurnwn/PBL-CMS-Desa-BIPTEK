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

    public function index()
    {
        return Inertia::render('Dashboard/Demografi/Index', [
            'demografi' => Demografi::all(),
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
        return redirect()->route('dashboard.demografi.index')->with('success', 'Demografi Berhasil Ditambahkan');
    }

    public function edit(Demografi $demografi)
    {
        return Inertia::render('Dashboard/Demografi/Edit', [
            'demografi' => $demografi,
        ]);
    }

    public function update(UpdateDemografiRequest $request, Demografi $demografi)
    {
        $demografiData = $request->all();

        $demografi->update($demografiData);

        $this->logHelper->logActivity('update', 'demografi', 'Memperbarui demografi ' . $request->title);
        return redirect()->route('dashboard.demografi.index')->with('success', 'Demografi Berhasil Diperbarui');
    }
}
