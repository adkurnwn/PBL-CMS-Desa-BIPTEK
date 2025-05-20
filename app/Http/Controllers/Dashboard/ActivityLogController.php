<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index(Request $request, $page = 1)
    {
        $perPage = 25; // Show a reasonable amount of records per page
        $sortField = 'time'; // Always sort by time/created_at
        $sortDirection = $request->input('direction', 'desc'); // Default direction is descending
        
        // Create query with sorting and ensure user relationship includes photo_profile
        $query = ActivityLog::with(['user' => function($query) {
            $query->select('id', 'name', 'photo_profile'); // Include photo_profile
        }])->orderBy('created_at', $sortDirection);
        
        // Count total records (for debugging)
        $totalRecords = $query->count();
        
        // Paginate results
        $logs = $query->paginate($perPage, ['*'], 'page', $page);
        

        return Inertia::render('Dashboard/Dashboard', [
            'logs' => $logs,
            'currentPage' => (int)$page,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }
}
