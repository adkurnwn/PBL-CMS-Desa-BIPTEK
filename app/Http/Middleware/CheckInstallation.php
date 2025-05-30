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
        $installed = env('APP_INSTALLED', false) === true &&
            Schema::hasTable('users') &&

            \App\Models\Profile::count() > 0;  // Add this check

        // Installation routes that should be accessible even if partially installed
        $installationRoutes = ['install/setup-profile', 'install/store-profile', 'install/finish'];
        $isInstallationRoute = $request->is('install*');
        $isExemptRoute = $request->is($installationRoutes);

        // If accessing install routes but fully installed, redirect to home
        if ($isInstallationRoute && $installed && !$isExemptRoute) {
            return redirect('/');
        }

        // If accessing non-install routes but not installed, redirect to install
        if (!$isInstallationRoute && !$isExemptRoute && !$request->is('assets*') && !$installed) {
            // Check if we need to go to setup profile
            if (Schema::hasTable('users') && \App\Models\Profile::count() == 0) {
                return redirect()->route('install.setupProfile');
            }
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
