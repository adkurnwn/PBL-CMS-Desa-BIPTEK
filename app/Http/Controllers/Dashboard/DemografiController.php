<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Demografi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    public function store(Request $request)
    {
        $rules = [
            'penduduk_pria' => 'required',
            'penduduk_wanita' => 'required',
            'tahun' => 'required',
            'jumlah_peserta_bpjs' => 'required',
            'jumlah_pindah_keluar' => 'required',
            'jumlah_pendatang' => 'required',
            'tamat_pt' => 'required',
            'tamat_sma' => 'required',
            'tamat_smp' => 'required',
            'tamat_sd' => 'required',
            'jumlah_lansia' => 'required',
            'jumlah_dewasa' => 'required',
            'jumlah_remaja' => 'required',
            'jumlah_anak' => 'required',
            'jumlah_penduduk' => 'required',
            'jumlah_keluarga' => 'required',
        ];

        $demografiData = $request->validate($rules);
        $demografiData['user_id'] = Auth::id();

        Demografi::create($demografiData);

        return redirect()->route('dashboard.demografi');
    }

    public function edit(Demografi $demografi)
    {
        return Inertia::render('Dashboard/Demografi/Edit', [
            'demografi' => $demografi,
        ]);
    }

    public function update(Request $request, Demografi $demografi)
    {
        $rules = [
            'penduduk_pria' => 'required',
            'penduduk_wanita' => 'required',
            'tahun' => 'required',
            'jumlah_peserta_bpjs' => 'required',
            'jumlah_pindah_keluar' => 'required',
            'jumlah_pendatang' => 'required',
            'tamat_pt' => 'required',
            'tamat_sma' => 'required',
            'tamat_smp' => 'required',
            'tamat_sd' => 'required',
            'jumlah_lansia' => 'required',
            'jumlah_dewasa' => 'required',
            'jumlah_remaja' => 'required',
            'jumlah_anak' => 'required',
            'jumlah_penduduk' => 'required',
            'jumlah_keluarga' => 'required',
        ];

        $demografiData = $request->validate($rules);

        $demografi->update($demografiData);

        return redirect()->route('dashboard.demografi');
    }
}
