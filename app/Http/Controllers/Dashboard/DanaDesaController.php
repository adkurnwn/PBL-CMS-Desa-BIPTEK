<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\DanaDesa;
use App\Helpers\SlugHelper;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreDanaDesaRequest;
use App\Http\Requests\UpdateDanaDesaRequest;

class DanaDesaController extends Controller
{
    protected $logHelper;

    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

    public function index(Request $request)
    {
        // Get query parameters
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $perPage = 25;
        
        // Get filter parameters
        $filters = [];
        if ($request->has('year')) {
            $filters['year'] = $request->input('year');
        }
        
        // Start building the query
        $query = DanaDesa::query();
        
        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }
        
        // Apply year filter if provided
        if (isset($filters['year'])) {
            $query->whereYear('date', $filters['year']);
        }
        
        // Apply sorting
        $query->orderBy($sort, $direction);
        
        // Get paginated results - ensure to use the current page from request
        $page = $request->input('page', 1);
        $danaDesa = $query->paginate($perPage, ['*'], 'page', $page)->withQueryString();
        
        // Get available years for the filter - using date column
        $availableYears = DanaDesa::select(DB::raw('YEAR(date) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(function($year) {
                return ['id' => $year, 'name' => $year];
            })
            ->toArray();
        
        return Inertia::render('Dashboard/DanaDesa/Index', [
            'danaDesa' => $danaDesa,
            'availableYears' => $availableYears,
            'search' => $search,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
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
            // Generate a sanitized filename from the title
            $sanitizedTitle = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $request->title);
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = $sanitizedTitle . '.' . $extension;

            // Store the file with the custom filename
            $path = $request->file('file')->storeAs('dana-desa-documents', $filename, 'public');
            $data['file'] = $path;
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
        $data['slug'] = SlugHelper::generateUniqueSlug(DanaDesa::class, $request->title);

        if ($request->hasFile('file')) {
            if ($danaDesa->file) {
                Storage::disk('public')->delete($danaDesa->file);
            }

            // Generate a sanitized filename from the title
            $sanitizedTitle = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $request->title);
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = $sanitizedTitle . '.' . $extension;

            // Store the file with the custom filename
            $path = $request->file('file')->storeAs('dana-desa-documents', $filename, 'public');
            $data['file'] = $path;
        } else {
            unset($data['file']);
        }

        $danaDesa->update($data);

        $this->logHelper->logActivity('update', 'dana_desa', 'Memperbarui dana desa ' . $danaDesa->title);
        return redirect()->route('dashboard.dana-desa.index')->with('success', 'Dana Desa Berhasil Diperbarui');
    }
}
