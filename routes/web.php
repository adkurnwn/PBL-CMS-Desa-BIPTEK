<?php

use App\Models\News;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return Inertia::render('FrontEnd/HomeView');
})->name('home');

Route::get('/profildesa', function () {
    return Inertia::render('FrontEnd/ProfileView');
})->name('profil-desa');

Route::get('/informasi', function () {
    return Inertia::render('FrontEnd/InformasiView');
})->name('informasi');

Route::get('/layanan', function () {
    return Inertia::render('FrontEnd/LayananView');
})->name('layanan');

//TESTING ROUTES
Route::get('/berita', function () {
    return Inertia::render('FrontEnd/BeritaListTest');
})->name('berita');

Route::get('/berita/{id}', function ($id) {
    return Inertia::render('FrontEnd/BeritaDetail', [
        'id' => $id
    ]);
})->name('berita.show');

Route::get('/berita-test/{slug}', function ($slug) {
    $news = News::where('slug', $slug)->firstOrFail();
    return Inertia::render('FrontEnd/BeritaDetailTest', [
        'slug' => $slug
    ]);
})->name('berita.show');

Route::get('/berita-test/{id}', function ($id) {
    return Inertia::render('FrontEnd/BeritaDetailTest', [
        'id' => $id
    ]);
})->name('berita.show');

Route::get('/produk-hukum-test', function () {
    return Inertia::render('FrontEnd/ProdukHukumListTest');
})->name('produk-hukum');

Route::get('/produk-hukum-test/{id}', function ($id) {
    return Inertia::render('FrontEnd/ProdukHukumDetailTest', [
        'id' => $id
    ]);
})->name('produk-hukum.show');

Route::get('/event-test', function () {
    return Inertia::render('FrontEnd/EventListTest');
})->name('event-test');

Route::middleware(['auth', 'user.status'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
