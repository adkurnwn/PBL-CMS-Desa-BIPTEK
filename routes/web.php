<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\UserManagementController;

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
    Route::prefix('admin')->group(function () {
        // Dashboard accessible to anyone with view_dashboard permission
        Route::middleware(['permission:view_dashboard'])->get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');

        // User management routes
        Route::middleware(['permission:read_users'])->get('/users', [UserManagementController::class, 'index'])->name('admin.users');
        Route::middleware(['permission:create_users'])->get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::middleware(['permission:create_users'])->post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::middleware(['permission:update_users'])->get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::middleware(['permission:update_users'])->put('/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::middleware(['permission:delete_users'])->delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

        // Role management routes
        Route::middleware(['permission:manage_roles'])->group(function () {
            Route::get('/roles', [RoleManagementController::class, 'index'])->name('admin.roles');
            Route::get('/roles/{role}/edit', [RoleManagementController::class, 'edit'])->name('admin.roles.edit');
            Route::put('/roles/{role}', [RoleManagementController::class, 'update'])->name('admin.roles.update');
        });

        // Department routes
        Route::middleware(['permission:read_positions'])->get('/positions', [PositionController::class, 'index'])->name('admin.positions');
        Route::middleware(['permission:create_positions'])->get('/positions/create', [PositionController::class, 'create'])->name('admin.positions.create');
        Route::middleware(['permission:create_positions'])->post('/positions', [PositionController::class, 'store'])->name('admin.positions.store');
        Route::middleware(['permission:update_positions'])->get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('admin.positions.edit');
        Route::middleware(['permission:update_positions'])->put('/positions/{position}', [PositionController::class, 'update'])->name('admin.positions.update');
        Route::middleware(['permission:delete_positions'])->delete('/positions/{position}', [PositionController::class, 'destroy'])->name('admin.positions.destroy');
    });
});
  
});
  
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
