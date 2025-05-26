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
        $perPage = 25;
        $sortField = 'time';
        $sortDirection = $request->input('direction', 'desc');
        $searchQuery = $request->input('search', '');

        $query = ActivityLog::with(['user' => function ($query) {
            $query->select('id', 'name', 'photo_profile');
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
        $logs = $query->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Dashboard/Dashboard', [
            'logs' => $logs,
            'currentPage' => (int)$page,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'search' => $searchQuery,
        ]);
    }
}
