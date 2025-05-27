<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $type = $request->query('type');
            $cacheKey = 'categories.index.{$type}';
            $categories = Cache::remember($cacheKey, 600, function () use ($type) {
                return Category::where('category_type', $type)->get();
            });
            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category data not found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
