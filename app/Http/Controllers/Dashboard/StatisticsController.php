<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Dashboard/Home/Statistics', [
            
        ]);
    }
}
