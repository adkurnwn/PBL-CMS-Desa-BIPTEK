<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index(Request $request, $page = null)
    {
        // Get page from request parameter or route parameter
        $page = $request->input('page', $page ?? 1);
        $page = (int)$page;
        
        $perPage = 25; // Show a reasonable amount of records per page
        $sortField = 'time'; // Always sort by time/created_at
        $sortDirection = $request->input('direction', 'desc'); // Default direction is descending
        $searchQuery = $request->input('search', ''); // Get search query from request
        
        // Create query with sorting and ensure user relationship includes photo_profile
        $query = ActivityLog::with(['user' => function($query) {
            $query->select('id', 'name', 'photo_profile'); // Include photo_profile
        }]);

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('action', 'like', "%{$searchQuery}%")
                    ->orWhere('module', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                    ->orWhereHas('user', function ($userQuery) use ($searchQuery) {
                        $userQuery->where('name', 'like', "%{$searchQuery}%");
                    });
            });
        }

        $query->orderBy('created_at', $sortDirection);
        
        // Paginate results with proper page parameter
        $logs = $query->paginate($perPage, ['*'], 'page', $page)
                     ->withQueryString(); // Preserve query parameters in pagination links

        return Inertia::render('Dashboard/Home/LogActivity', [
            'logs' => $logs,
            'currentPage' => $logs->currentPage(),
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'search' => $searchQuery,
        ]);
    }
}
