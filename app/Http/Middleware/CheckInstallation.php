<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Profile;
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
        // Check if the application is installed
        $installed = env('APP_INSTALLED', false) === true;

        $isInstallationRoute = $request->is('install*');

        // If accessing install routes but fully installed, redirect to home
        if ($isInstallationRoute && $installed) {
            return redirect('/');
        }

        // If accessing non-install routes but not installed, redirect to install
        if (!$isInstallationRoute && !$request->is('assets*') && !$installed) {
            return redirect()->route('install.index');
        }

        // Handle specific installation step redirections
        if ($isInstallationRoute && !$installed) {
            // Check if admin setup is complete
            $adminExists = $this->checkAdminExists();

            // Check if profile setup is complete
            $profileExists = $this->checkProfileExists();

            // If trying to access admin setup but admin already exists, redirect to next step
            if ($request->is('install/admin') && $adminExists) {
                if (!$profileExists) {
                    return redirect()->route('install.setupProfile');
                } else {
                    return redirect()->route('install.finish');
                }
            }

            // If trying to access profile setup but profile already exists, redirect to finish
            if ($request->is('install/setup-profile') && $profileExists) {
                return redirect()->route('install.finish');
            }

            // If trying to access profile setup but admin doesn't exist, redirect to admin setup
            if ($request->is('install/setup-profile') && !$adminExists) {
                return redirect()->route('install.admin');
            }
        }

        return $next($request);
    }

    /**
     * Check if admin user exists
     */
    private function checkAdminExists(): bool
    {
        try {
            if (!Schema::hasTable('users')) {
                return false;
            }

            return User::whereHas('roles', function ($query) {
                $query->where('name', 'Admin');
            })->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if profile exists
     */
    private function checkProfileExists(): bool
    {
        try {
            if (!Schema::hasTable('profiles')) {
                return false;
            }

            return Profile::exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}
