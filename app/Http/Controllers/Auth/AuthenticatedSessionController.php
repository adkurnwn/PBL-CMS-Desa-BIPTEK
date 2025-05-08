<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    protected $logHelper;
    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        $profileDesa = Profile::latest()->first();
        $profile['logo'] = $profileDesa->logo ?? null;
        $profile['name'] = $profileDesa->name ?? null;
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'profile' => $profile,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->logHelper->logActivity('login', 'user', 'Pengguna ' . Auth::user()->name . ' logged in');
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->logHelper->logActivity('logout', 'user', 'Pengguna ' . Auth::user()->name . ' logged out');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
