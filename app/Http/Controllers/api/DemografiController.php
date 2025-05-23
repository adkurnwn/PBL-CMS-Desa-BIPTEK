<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Demografi;
use Illuminate\Http\Request;

class DemografiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $demografi = Demografi::where('tahun', $slug)->first();

        if (!$demografi) {
            return response()->json(['message' => 'Demografi not found'], 404);
        }

        return response()->json($demografi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Demografi $demografi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Demografi $demografi)
    {
        //
    }
}
