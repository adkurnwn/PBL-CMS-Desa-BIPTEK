<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Demografi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validation\StoreDemografiRequest;
use App\Http\Requests\Validation\UpdateDemografiRequest;
use Illuminate\Support\Facades\Auth;

class DemografiController extends Controller
{
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

        return redirect()->route('dashboard.demografi')->with('success', 'Demografi Created Successfully');
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

        return redirect()->route('dashboard.demografi')->with('success', 'Demografi Updated Successfully');
    }
}
