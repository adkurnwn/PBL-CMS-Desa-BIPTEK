<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the application is installed by testing if specific tables exist
        $installed = env('APP_INSTALLED', false) &&
            Schema::hasTable('users') &&
            Schema::hasTable('settings');

        // If accessing install routes but already installed, redirect to home
        if ($request->is('install*') && $installed) {
            return redirect('/');
        }

        // If accessing any non-install route but not installed, redirect to install
        if (!$request->is('install*') && !$request->is('assets*') && !$installed) {
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
