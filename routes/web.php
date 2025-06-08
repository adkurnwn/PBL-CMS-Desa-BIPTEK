<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Services\StatisticsService;

Route::get('/', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Home', request());
    return Inertia::render('FrontEnd/HomeView');
})->name('home');

Route::get('/profile', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Profil Desa', request());
    return Inertia::render('FrontEnd/ProfileView');
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
        return Inertia::render('FrontEnd/BeritaDetail', [
            'slug' => $slug
        ]);
    })->name('berita.show');
});

Route::prefix('produk-hukum')->group(function () {
    Route::get('/', function (StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Produk Hukum', request());
        return Inertia::render('FrontEnd/ProdukHukumList');
    })->name('produk-hukum');

    Route::get('/{slug}', function ($slug, StatisticsService $statisticsService) {
        $statisticsService->trackFeatureVisit('Produk Hukum', request());
        return Inertia::render('FrontEnd/ProdukHukumDetail', [
            'slug' => $slug
        ]);
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

Route::get('/galeri-desa', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Galeri', request());
    return Inertia::render('FrontEnd/GaleriList');
});

Route::get('/statistik-penduduk', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Statistik', request());
    return Inertia::render('FrontEnd/StatistikPenduduk');
});

Route::get('/galeri-desa-detail', function (StatisticsService $statisticsService) {
    $statisticsService->trackFeatureVisit('Galeri', request());
    return Inertia::render('FrontEnd/GaleriDetail');
});

Route::get('/aparat-desa', function () {
    return Inertia::render('FrontEnd/AparatDesa');
});


require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
