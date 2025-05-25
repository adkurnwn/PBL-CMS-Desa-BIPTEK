<?php

use App\Models\News;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;

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

Route::get('/produk-unggulan/{slug}', function ($slug) {
    return Inertia::render('FrontEnd/ProdukUnggulanDetail', [
        'slug' => $slug
    ]);
})->name('produk-desa.detail');

Route::middleware(['auth', 'user.status'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
