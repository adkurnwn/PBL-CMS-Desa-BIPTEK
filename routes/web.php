<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Services\StatisticsService;
use App\Http\Controllers\MetaCrawler\HomeController;
use App\Http\Controllers\MetaCrawler\BeritaController;
use App\Http\Controllers\MetaCrawler\ProfileController;
use App\Http\Controllers\MetaCrawler\ProdukHukumController;

// routes/web.php

Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::get('/requirements', [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/permissions', [InstallController::class, 'permissions'])->name('permissions');
    Route::get('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/database', [InstallController::class, 'setDatabase'])->name('setDatabase');
    Route::post('/database/test', [InstallController::class, 'testDatabaseConnection'])->name('testConnection'); // Add this line
    Route::get('/admin', [InstallController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'setAdmin'])->name('setAdmin');
    Route::get('/setup-profile', [InstallController::class, 'setupProfile'])->name('setupProfile');
    Route::post('/setup-profile', [InstallController::class, 'storeProfile'])->name('storeProfile');
    Route::get('/finish', [InstallController::class, 'finish'])->name('finish');
    Route::post('/finalize', [InstallController::class, 'finalize'])->name('finalize');
});

Route::fallback(function ($name = null) {
    return app()->make(InstallController::class)->handleProfileNotFound($name);
});


Route::get('/', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Home', request());
    return app(HomeController::class)->index();
})->name('home');

Route::get('/profile', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Profil Desa', request());
    return app(ProfileController::class)->index();
})->name('profil-desa');

Route::get('/informasi', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Informasi & Berita', request());
    return Inertia::render('FrontEnd/InformasiView');
})->name('informasi');

Route::get('/layanan', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Layanan Publik', request());
    return Inertia::render('FrontEnd/LayananView');
})->name('layanan');

Route::prefix('berita')->group(function () {
    Route::get('/', function (StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Berita', request());
        return Inertia::render('FrontEnd/BeritaList');
    })->name('berita');

    Route::get('/{slug}', function ($slug, StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Berita', request());
        return app(BeritaController::class)->show($slug);
    })->name('berita.show');
});

Route::prefix('produk-hukum')->group(function () {
    Route::get('/', function (StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Produk Hukum', request());
        return Inertia::render('FrontEnd/ProdukHukumList');
    })->name('produk-hukum');

    Route::get('/{slug}', function ($slug, StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Produk Hukum', request());
        return app(ProdukHukumController::class)->show($slug);
    })->name('produk-hukum.show');
});

Route::get('/event', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Agenda', request());
    return Inertia::render('FrontEnd/EventList');
})->name('event');

Route::prefix('dana-desa')->group(function () {
    Route::get('/', function (StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Dana Desa', request());
        return Inertia::render('FrontEnd/LaporanDana');
    })->name('dana-desa');

    Route::get('/{slug}', function ($slug, StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Dana Desa', request());
        return Inertia::render('FrontEnd/LaporanDanaDetail', [
            'slug' => $slug
        ]);
    })->name('dana-desa.show');
});

Route::prefix('produk-unggulan')->group(function () {
    Route::get('/', function (StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Produk Unggulan', request());
        return Inertia::render('FrontEnd/ProdukUnggulan');
    });

    Route::get('/{slug}', function ($slug, StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Produk Unggulan', request());
        return Inertia::render('FrontEnd/ProdukUnggulanDetail', [
            'slug' => $slug
        ]);
    })->name('produk-desa.detail');
});

Route::prefix('galeri')->group(function () {
    Route::get('/', function (StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Galeri', request());
        return Inertia::render('FrontEnd/GaleriList');
    });

    Route::get('/{slug}', function ($slug, StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Galeri', request());
    return Inertia::render('FrontEnd/GaleriDetail', [
        'slug' => $slug
    ]);
    })->name('galeri.detail');
});

Route::get('/data-demografi', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Statistik', request());
    return Inertia::render('FrontEnd/StatistikPenduduk');
});

Route::get('/struktur-organisasi', function () {
    return Inertia::render('FrontEnd/AparatDesa');
});


require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
