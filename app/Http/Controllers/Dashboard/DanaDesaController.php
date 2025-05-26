<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\DanaDesa;
use App\Helpers\SlugHelper;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validation\StoreDanaDesaRequest;
use App\Http\Requests\Validation\UpdateDanaDesaRequest;
use App\Services\MediaService;

class DanaDesaController extends Controller
{
    protected $mediaService;
    protected $logHelper;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
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

    public function store(StoreDanaDesaRequest $request)
    {
        $data = $request->all();
        $data['slug'] = SlugHelper::generateUniqueSlug(DanaDesa::class, $request->title);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('file')) {
            $data['file'] = $this->mediaService->handleDocumentUpload(
                $request->file('file'),
                $request->title,
                'dana-desa-documents'
            );
        }

        DanaDesa::create($data);

        $this->logHelper->logActivity('create', 'dana_desa', 'Menambahkan dana desa ' . $request->title);
        return redirect()->route('dashboard.dana-desa.index')->with('success', 'Dana Desa Berhasil Ditambahkan');
    }

    public function edit($slug)
    {
        $danaDesa = DanaDesa::where('slug', $slug)->firstOrFail();
        return Inertia::render('Dashboard/DanaDesa/Edit', [
            'danaDesa' => $danaDesa
        ]);
    }

    public function update(UpdateDanaDesaRequest $request, $slug)
    {
        $danaDesa = DanaDesa::where('slug', $slug)->firstOrFail();

        $data = $request->all();
        if ($danaDesa->title !== $request->title) {
            $data['slug'] = SlugHelper::generateUniqueSlug(DanaDesa::class, $request->title);
        }

        if ($request->hasFile('file')) {
            $data['file'] = $this->mediaService->handleDocumentUpload(
                $request->file('file'),
                $request->title,
                'dana-desa-documents',
                $danaDesa->file
            );
        } else {
            unset($data['file']);
        }

        $danaDesa->update($data);

        $this->logHelper->logActivity('update', 'dana_desa', 'Memperbarui dana desa ' . $danaDesa->title);
        return redirect()->route('dashboard.dana-desa.index')->with('success', 'Dana Desa Berhasil Diperbarui');
    }
}
