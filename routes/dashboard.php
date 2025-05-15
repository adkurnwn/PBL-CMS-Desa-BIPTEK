<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\NewsController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\GalleryController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\DanaDesaController;
use App\Http\Controllers\Dashboard\FeedbackController;
use App\Http\Controllers\Dashboard\PositionController;
use App\Http\Controllers\Dashboard\DemografiController;
use App\Http\Controllers\Dashboard\ProdukDesaController;
use App\Http\Controllers\Dashboard\ProdukHukumController;
use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\KomentarBeritaController;
use App\Http\Controllers\Dashboard\RoleManagementController;
use App\Http\Controllers\Dashboard\UserManagementController;
use App\Http\Controllers\Dashboard\KomentarProdukHukumController;
use App\Http\Controllers\Dashboard\RecoveryController;

Route::middleware(['auth', 'verified', 'user.status'])->group(function () {
    Route::middleware(['permission:manage_activity-logs'])->get('/dashboard', [ActivityLogController::class, 'index'])->name('dashboard');
    Route::prefix('dashboard')->group(function () {
        // User management routes
        Route::middleware(['permission:manage_users'])->group(function () {
            Route::resource('users', UserManagementController::class, [
                'as' => 'dashboard'
            ]);
        });

        // Role management routes
        Route::middleware(['permission:manage_roles'])->group(function () {
            Route::resource('roles', RoleManagementController::class, [
                'as' => 'dashboard',
                'only' => ['index', 'edit', 'update']
            ]);
            Route::post('/roles/batch-update', [RoleManagementController::class, 'batchUpdate'])->name('roles.batch-update');
        });

        Route::middleware(['permission:manage_profiles'])->group(function () {
            Route::resource('profiles', ProfileController::class, [
                'as' => 'dashboard',
                'only' => ['index', 'edit', 'update']
            ]);
        });

        Route::middleware(['permission:manage_news'])->group(function () {
            Route::resource('news', NewsController::class, [
                'as' => 'dashboard',
                'except' => ['show']
            ]);
            Route::delete('/news/{news}/destroy', [NewsController::class, 'destroy'])->name('dashboard.news.destroy')->withTrashed();
            Route::get('/news/archive', [NewsController::class, 'archive'])->name('dashboard.news.archive');
            Route::get('/news/{news}/restore', [NewsController::class, 'restore'])->name('dashboard.news.restore')->withTrashed();
        });

        Route::middleware(['permission:manage_galleries'])->group(function () {
            Route::resource('galleries', GalleryController::class, [
                'as' => 'dashboard',
                'except' => ['show']
            ]);
            Route::delete('/galleries/{gallery}/destroy', [GalleryController::class, 'destroy'])->name('dashboard.galleries.destroy')->withTrashed();
            Route::get('/galleries/archive', [GalleryController::class, 'archive'])->name('dashboard.galleries.archive');
            Route::get('/galleries/{gallery}/restore', [GalleryController::class, 'restore'])->name('dashboard.galleries.restore')->withTrashed();
        });

        Route::middleware(['permission:manage_events'])->group(function () {
            Route::resource('events', EventController::class, [
                'as' => 'dashboard'
            ]);
        });

        Route::middleware(['permission:manage_produk-hukum'])->group(function () {
            Route::resource('produk-hukum', ProdukHukumController::class, [
                'as' => 'dashboard',
                'except' => ['destroy']
            ]);
        });

        Route::middleware(['permission:manage_dana-desa'])->group(function () {
            Route::resource('dana-desa', DanaDesaController::class, [
                'as' => 'dashboard',
                'except' => ['destroy']
            ]);
        });

        Route::middleware(['permission:manage_positions'])->group(function () {
            Route::resource('positions', PositionController::class, [
                'as' => 'dashboard'
            ]);
        });

        Route::middleware(['permission:manage_produk-desa'])->group(function () {
            Route::resource('produk-desa', ProdukDesaController::class, [
                'as' => 'dashboard'
            ]);
        });

        Route::middleware(['permission:manage_demografi'])->group(function () {
            Route::resource('demografi', DemografiController::class, [
                'as' => 'dashboard',
                'except' => ['destroy', 'show']
            ]);
        });

        Route::middleware(['permission:manage_comments'])->group(function () {
            Route::get('/comments', [KomentarBeritaController::class, 'index'])->name('dashboard.comments');
            Route::get('/produk-hukum-comments', [KomentarProdukHukumController::class, 'index'])->name('dashboard.produk-hukum.index-comments');
            Route::patch('/comments/{comment}/approve', [KomentarBeritaController::class, 'approve'])->name('dashboard.comments.approve');
            Route::patch('/produk-hukum-comments/{comment}/approve', [KomentarProdukHukumController::class, 'approve'])->name('dashboard.produk-hukum.index-comments.approve');
            Route::delete('/comments/{comment}', [KomentarBeritaController::class, 'destroy'])->name('dashboard.comments.destroy');
            Route::delete('/produk-hukum-comments/{comment}', [KomentarProdukHukumController::class, 'destroy'])->name('dashboard.produk-hukum.index-comments.destroy');
        });

        Route::middleware(['permission:manage_feedbacks'])->group(function () {
            Route::resource('feedbacks', FeedbackController::class, [
                'as' => 'dashboard',
                'only' => ['index', 'show']
            ]);
            Route::post('/feedbacks/{feedback}/respond', [FeedbackController::class, 'respond'])->name('dashboard.feedbacks.respond');
        });

        Route::middleware(['permission:manage_recovery'])->group(function () {
            Route::get('/recovery', [RecoveryController::class, 'index'])->name('dashboard.recovery.index');
            Route::post('/recovery/create', [RecoveryController::class, 'create'])->name('dashboard.recovery.create');
            Route::get('/recovery/download/{fileName}', [RecoveryController::class, 'download'])->name('dashboard.recovery.download');
            Route::post('/recovery/restore/{fileName}', [RecoveryController::class, 'restore'])->name('dashboard.recovery.restore');
            Route::delete('/recovery/delete/{fileName}', [RecoveryController::class, 'delete'])->name('dashboard.recovery.delete');
        });
    });
});
