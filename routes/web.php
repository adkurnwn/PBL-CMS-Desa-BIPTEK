<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;

// routes/web.php

Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::get('/requirements', [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/permissions', [InstallController::class, 'permissions'])->name('permissions');
    Route::get('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/database', [InstallController::class, 'setDatabase'])->name('setDatabase');
    Route::get('/admin', [InstallController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'setAdmin'])->name('setAdmin');
    Route::get('/finish', [InstallController::class, 'finish'])->name('finish');
});


Route::get('/', function () {
    return Inertia::render('FrontEnd/HomeView');
})->name('home');

Route::get('/profile', function () {
    return Inertia::render('FrontEnd/ProfileView');
})->name('profil-desa');

Route::get('/informasi', function () {
    return Inertia::render('FrontEnd/InformasiView');
})->name('informasi');

Route::get('/layanan', function () {
    return Inertia::render('FrontEnd/LayananView');
})->name('layanan');

Route::prefix('berita')->group(function () {
    Route::get('/', function () {
        return Inertia::render('FrontEnd/BeritaList');
    })->name('berita');

    Route::get('/{slug}', function ($slug) {
        return Inertia::render('FrontEnd/BeritaDetail', [
            'slug' => $slug
        ]);
    })->name('berita.show');
});

Route::prefix('produk-hukum')->group(function () {
    Route::get('/', function () {
        return Inertia::render('FrontEnd/ProdukHukumList');
    })->name('produk-hukum');

    Route::get('/{slug}', function ($slug) {
        return Inertia::render('FrontEnd/ProdukHukumDetail', [
            'slug' => $slug
        ]);
    })->name('produk-hukum.show');
});

Route::get('/event-test', function () {
    return Inertia::render('FrontEnd/EventListTest');
})->name('event-test');

Route::get('/laporan-dana-desa', function () {
    return Inertia::render('FrontEnd/LaporanDana');
});


Route::get('/produk-unggulan', function () {
    return Inertia::render('FrontEnd/ProdukUnggulan');
});

Route::get('/galeri-desa', function () {
    return Inertia::render('FrontEnd/GaleriList');
});

Route::get('/galeri-desa-detail', function () {
    return Inertia::render('FrontEnd/GaleriDetail');
});

Route::get('/produk-unggulan/{slug}', function ($slug) {
    return Inertia::render('FrontEnd/ProdukUnggulanDetail', [
        'slug' => $slug
    ]);
})->name('produk-desa.detail');

require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
