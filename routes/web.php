<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

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

Route::get('/event', function () {
    return Inertia::render('FrontEnd/EventList');
})->name('event');


Route::prefix('laporan-dana-desa')->group(function () {
    Route::get('/', function () {
        return Inertia::render('FrontEnd/LaporanDana');
    })->name('dana-desa');

    Route::get('/{slug}', function ($slug) {
        return Inertia::render('FrontEnd/LaporanDanaDetail', [
            'slug' => $slug
        ]);
    })->name('dana-desa.show');
});

Route::prefix('produk-unggulan')->group(function () {
    Route::get('/', function () {
        return Inertia::render('FrontEnd/ProdukUnggulan');
    });

    Route::get('/{slug}', function ($slug) {
        return Inertia::render('FrontEnd/ProdukUnggulanDetail', [
            'slug' => $slug
        ]);
    })->name('produk-desa.detail');
});

Route::get('/galeri-desa', function () {
    return Inertia::render('FrontEnd/GaleriList');
});

Route::get('/galeri-desa-detail', function () {
    return Inertia::render('FrontEnd/GaleriDetail');
});



require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
