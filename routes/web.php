<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Dashboard\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\RoleManagementController;
use App\Http\Controllers\Dashboard\UserManagementController;
use App\Http\Controllers\Dashboard\ProfileController as DesaProfileController;

Route::get('/', function () {
    return view('home');
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
});

Route::get('/profildesa', function () {
    return view('profil');
});

Route::get('/informasi', function () {
    return view('informasi');
});

Route::get('/layanan', function () {
    return view('layanan');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin area routes with permission-based middleware


Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['permission:view_dashboard'])->get('/dashboard', function () {
        return Inertia::render('Dashboard/Dashboard');
    })->name('dashboard');
    Route::prefix('dashboard')->group(function () {
        // Dashboard accessible to anyone with view_dashboard permission

        // User management routes
        Route::middleware(['permission:read_users'])->get('/users', [UserManagementController::class, 'index'])->name('dashboard.users');
        Route::middleware(['permission:create_users'])->get('/users/create', [UserManagementController::class, 'create'])->name('dashboard.users.create');
        Route::middleware(['permission:create_users'])->post('/users', [UserManagementController::class, 'store'])->name('dashboard.users.store');
        Route::middleware(['permission:update_users'])->get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('dashboard.users.edit');
        Route::middleware(['permission:update_users'])->put('/users/{user}', [UserManagementController::class, 'update'])->name('dashboard.users.update');
        Route::middleware(['permission:delete_users'])->delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('dashboard.users.destroy');

        // Role management routes
        Route::middleware(['permission:manage_roles'])->group(function () {
            Route::get('/roles', [RoleManagementController::class, 'index'])->name('dashboard.roles');
            Route::get('/roles/{role}/edit', [RoleManagementController::class, 'edit'])->name('dashboard.roles.edit');
            Route::put('/roles/{role}', [RoleManagementController::class, 'update'])->name('dashboard.roles.update');
        });

        //Profile management routes
        Route::middleware(['permission:read_profiles'])->get('/profiles', [DesaProfileController::class, 'index'])->name('dashboard.profiles');
        Route::middleware(['permission:create_profiles'])->get('/profiles/create', [DesaProfileController::class, 'create'])->name('dashboard.profiles.create');
        Route::middleware(['permission:create_profiles'])->post('/profiles', [DesaProfileController::class, 'store'])->name('dashboard.profiles.store');
        Route::middleware(['permission:update_profiles'])->get('/profiles/{profile}/edit', [DesaProfileController::class, 'edit'])->name('dashboard.profiles.edit');
        Route::middleware(['permission:update_profiles'])->put('/profiles/{profile}', [DesaProfileController::class, 'update'])->name('dashboard.profiles.update');
        Route::middleware(['permission:delete_profiles'])->delete('/profiles/{profile}', [DesaProfileController::class, 'destroy'])->name('dashboard.profiles.destroy');

        Route::middleware(['permission:read_news'])->get('/news', [NewsController::class, 'index'])->name('dashboard.news');
        Route::middleware(['permission:create_news'])->get('/news/create', [NewsController::class, 'create'])->name('dashboard.news.create');
        Route::middleware(['permission:create_news'])->post('/news', [NewsController::class, 'store'])->name('dashboard.news.store');
        Route::middleware(['permission:update_news'])->get('/news/{news}/edit', [NewsController::class, 'edit'])->name('dashboard.news.edit');
        Route::middleware(['permission:update_news'])->put('/news/{news}', [NewsController::class, 'update'])->name('dashboard.news.update');
        Route::middleware(['permission:delete_news'])->delete('/news/{news}', [NewsController::class, 'destroy'])->name('dashboard.news.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
});