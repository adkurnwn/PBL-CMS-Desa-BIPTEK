<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DepartementController;
use App\Http\Controllers\Admin\UserManagementController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');

        Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
        Route::get('/departements', [DepartementController::class, 'index'])->name('admin.departements');
        Route::get('/departements/create', [DepartementController::class, 'create'])->name('admin.departements.create');
        Route::post('/departements', [DepartementController::class, 'store'])->name('admin.departements.store');
        Route::get('/departements/{user}/edit', [DepartementController::class, 'edit'])->name('admin.departements.edit');
        Route::put('/departements/{user}', [DepartementController::class, 'update'])->name('admin.departements.update');
        Route::delete('/departements/{user}', [DepartementController::class, 'destroy'])->name('admin.departements.destroy');
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
