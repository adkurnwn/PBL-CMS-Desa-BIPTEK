<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\DanaDesa;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DanaDesaController extends Controller
{
    protected $logHelper;

    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

    public function index()
    {
        return Inertia::render('Dashboard/DanaDesa/Index', [
            'danaDesa' => DanaDesa::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/DanaDesa/Create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:2048',
            'link' => 'required',
            'date' => 'required'
        ];

        $request->validate($rules);
        $data = $request->all();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('dana-desa-documents', 'public');
            $data['file'] = $path;
        }

        DanaDesa::create($data);

        $this->logHelper->logActivity('create', 'dana_desa', 'Menambahkan dana desa ' . $request->title);
        return redirect()->route('dashboard.dana-desa')->with('success', 'Dana Desa Berhasil Ditambahkan');
    }

    public function edit(DanaDesa $danaDesa)
    {
        return Inertia::render('Dashboard/DanaDesa/Edit', [
            'danaDesa' => $danaDesa
        ]);
    }

    public function update(Request $request, DanaDesa $danaDesa)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:2048',
            'link' => 'required',
            'date' => 'required'
        ];

        $request->validate($rules);
        $data = $request->all();

        if ($request->hasFile('file')) {
            if ($danaDesa->file) {
                Storage::disk('public')->delete($danaDesa->file);
            }
            $path = $request->file('file')->store('dana-desa-documents', 'public');
            $data['file'] = $path;
        }

        $danaDesa->update($data);

        $this->logHelper->logActivity('update', 'dana_desa', 'Memperbarui dana desa ' . $danaDesa->title);
        return redirect()->route('dashboard.dana-desa')->with('success', 'Dana Desa Berhasil Diperbarui');
    }
}
