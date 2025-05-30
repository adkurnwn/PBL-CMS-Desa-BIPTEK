<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Profile;
use Illuminate\Support\Facades\Schema;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'photo_profile' => $request->user()->photo_profile,
                    'position' => $request->user()->position,
                    // Include the user's roles
                    'roles' => $request->user()->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                            'display_name' => ucfirst($role->name),
                        ];
                    }),
                    // Include user permissions for sidebar menu
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                ] : null,
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
            // Share the profile data with all requests - but only if table exists
            'profile' => $this->getProfile(),
        ];
    }

    /**
     * Safely get profile data, handling cases where table doesn't exist yet
     */
    private function getProfile()
    {
        try {
            // Check if we're in installation mode or table doesn't exist
            if (!Schema::hasTable('profiles')) {
                return null;
            }

            return Profile::first();
        } catch (\Exception $e) {
            // Return null if any database error occurs (e.g., during installation)
            return null;
        }
    }
}
