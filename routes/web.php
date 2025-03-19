<?php

use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\GalleryController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Dashboard\NewsController;
use App\Http\Controllers\Dashboard\ProdukHukumController;
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
        Route::middleware(['permission:update_profiles'])->get('/profiles/{profile}/edit', [DesaProfileController::class, 'edit'])->name('dashboard.profiles.edit');
        Route::middleware(['permission:update_profiles'])->put('/profiles/{profile}', [DesaProfileController::class, 'update'])->name('dashboard.profiles.update');

        Route::middleware(['permission:read_news'])->get('/news', [NewsController::class, 'index'])->name('dashboard.news');
        Route::middleware(['permission:create_news'])->get('/news/create', [NewsController::class, 'create'])->name('dashboard.news.create');
        Route::middleware(['permission:create_news'])->post('/news', [NewsController::class, 'store'])->name('dashboard.news.store');
        Route::middleware(['permission:update_news'])->get('/news/{news}/edit', [NewsController::class, 'edit'])->name('dashboard.news.edit');
        Route::middleware(['permission:update_news'])->put('/news/{news}', [NewsController::class, 'update'])->name('dashboard.news.update');
        Route::middleware(['permission:delete_news'])->delete('/news/{news}', [NewsController::class, 'destroy'])->name('dashboard.news.destroy');

        Route::middleware(['permission:read_galleries'])->get('/galleries', [GalleryController::class, 'index'])->name('dashboard.galleries');
        Route::middleware(['permission:create_galleries'])->get('/galleries/create', [GalleryController::class, 'create'])->name('dashboard.galleries.create');
        Route::middleware(['permission:create_galleries'])->post('/galleries', [GalleryController::class, 'store'])->name('dashboard.galleries.store');
        Route::middleware(['permission:update_galleries'])->get('/galleries/{gallery}/edit', [GalleryController::class, 'edit'])->name('dashboard.galleries.edit');
        Route::middleware(['permission:update_galleries'])->put('/galleries/{gallery}', [GalleryController::class, 'update'])->name('dashboard.galleries.update');
        Route::middleware(['permission:delete_galleries'])->delete('/galleries/{gallery}', [GalleryController::class, 'destroy'])->name('dashboard.galleries.destroy');

        Route::middleware(['permission:read_events'])->get('/event', [EventController::class, 'index'])->name('dashboard.events');
        Route::middleware(['permission:create_events'])->get('/event/create', [EventController::class, 'create'])->name('dashboard.events.create');
        Route::middleware(['permission:create_events'])->post('/event', [EventController::class, 'store'])->name('dashboard.events.store');
        Route::middleware(['permission:update_events'])->get('/event/{event}/edit', [EventController::class, 'edit'])->name('dashboard.events.edit');
        Route::middleware(['permission:update_events'])->put('/event/{event}', [EventController::class, 'update'])->name('dashboard.events.update');
        Route::middleware(['permission:delete_events'])->delete('/event/{event}', [EventController::class, 'destroy'])->name('dashboard.events.destroy');

        Route::middleware(['permission:read_produk-hukum'])->get('/produk-hukum', [ProdukHukumController::class, 'index'])->name('dashboard.produk-hukum');
        Route::middleware(['permission:create_produk-hukum'])->get('/produk-hukum/create', [ProdukHukumController::class, 'create'])->name('dashboard.produk-hukum.create');
        Route::middleware(['permission:create_produk-hukum'])->post('/produk-hukum', [ProdukHukumController::class, 'store'])->name('dashboard.produk-hukum.store');
        Route::middleware(['permission:update_produk-hukum'])->get('/produk-hukum/{produk-hukum}/edit', [ProdukHukumController::class, 'edit'])->name('dashboard.produk-hukum.edit');
        Route::middleware(['permission:update_produk-hukum'])->put('/produk-hukum/{produk-hukum}', [ProdukHukumController::class, 'update'])->name('dashboard.produk-hukum.update');
        Route::middleware(['permission:delete_produk-hukum'])->delete('/produk-hukum/{produk-hukum}', [ProdukHukumController::class, 'destroy'])->name('dashboard.produk-hukum.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
