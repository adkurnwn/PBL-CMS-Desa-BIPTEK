<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return Inertia::render('Dashboard/Dashboard', [
            'logs' => $logs,
        ]);
    }
}
