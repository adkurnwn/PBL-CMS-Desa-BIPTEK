<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function trackContentView(Request $request)
    {
        $request->validate([
            'category' => 'required|string|in:news,gallery,event,produk_hukum,dana_desa,produk_desa',
            'content_id' => 'required|integer',
        ]);

        $this->statisticsService->trackContentView(
            $request->category,
            $request->content_id,
            $request
        );

        return response()->json(['success' => true]);
    }

    public function trackFeatureVisit(Request $request)
    {
        $request->validate([
            'feature_name' => 'required|string',
        ]);

        $this->statisticsService->trackFeatureVisit(
            $request->feature_name,
            $request
        );

        return response()->json(['success' => true]);
    }
}
