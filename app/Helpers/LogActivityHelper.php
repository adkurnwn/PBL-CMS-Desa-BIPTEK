<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivityHelper
{
    function logActivity($action, $module = null, $description = null)
    {
        ActivityLog::create([
            'user_id'    => Auth::check() ? Auth::user()->id : null,
            'action'     => $action,
            'module'     => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
