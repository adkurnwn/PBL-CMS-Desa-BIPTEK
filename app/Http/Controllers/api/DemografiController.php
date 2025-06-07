<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Demografi\DemografiPreview;
use App\Models\Demografi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DemografiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            $query = Demografi::query();
            if ($search) {
                $query->where('tahun', 'like', '%' . $search . '%')
                    ->orWhere('jumlah_penduduk', 'like', '%' . $search . '%');
            }
            $demografi = $query->latest()->paginate(10);
            if ($demografi->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Demografi data found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => DemografiPreview::collection($demografi),
                'meta' => [
                    'current_page' => $demografi->currentPage(),
                    'last_page' => $demografi->lastPage(),
                    'total' => $demografi->total(),
                    'per_page' => $demografi->perPage()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }

    public function preview()
    {
        try {
            $demografi = Cache::remember('demografi_preview', now()->addDay(), function () {
                return Demografi::latest()->first();
            });
            if (!$demografi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demografi data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new DemografiPreview($demografi)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            $demografi = Demografi::where('tahun', $slug)->first();

            if (!$demografi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demografi not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $demografi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }
}
