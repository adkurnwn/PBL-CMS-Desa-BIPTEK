<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('install*')) {
            return $next($request);
        }

        // If not installed and not requesting /install route, redirect
        if (!$this->isInstalled()) {
            return redirect('/install');
        }

        return $next($request);
    }

    private function isInstalled()
    {
        return File::exists(storage_path('installed')) || env('APP_INSTALLED') === 'true';
    }
}
