<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\NewsController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\EventController;
use App\Http\Controllers\api\RegionController;
use App\Http\Controllers\api\GalleryController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\DanaDesaController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\FeedbackController;
use App\Http\Controllers\api\DemografiController;
use App\Http\Controllers\api\ProdukDesaController;
use App\Http\Controllers\api\ProdukHukumController;
use App\Http\Controllers\api\KomentarBeritaController;
use App\Http\Controllers\api\KomentarProdukHukumController;

Route::get('provinces', [RegionController::class, 'getProvinces']);
Route::get('regencies/{province_id}', [RegionController::class, 'getRegencies']);
Route::get('districts/{regency_id}', [RegionController::class, 'getDistricts']);
Route::get('villages/{district_id}', [RegionController::class, 'getVillages']);

Route::prefix('profile')->group(function () {
    Route::get('/layout', [ProfileController::class, 'layout']);
    Route::get('/profile', [ProfileController::class, 'profilePage']);
});

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);

    Route::get('/kepala-desa', [UserController::class, 'kepalaDesa']);
});

Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/preview', [NewsController::class, 'preview']);
    Route::get('/{slug}', [NewsController::class, 'show']);
});

Route::prefix('gallery')->group(function () {
    Route::get('/', [GalleryController::class, 'index']);
    Route::get('/highlight', [GalleryController::class, 'highlight']);
    Route::get('/{slug}', [GalleryController::class, 'show']);
});

Route::prefix('dana-desa')->group(function () {
    Route::get('/', [DanaDesaController::class, 'index']);
    Route::get('/{slug}', [DanaDesaController::class, 'show']);
});

Route::prefix('produk-desa')->group(function () {
    Route::get('/', [ProdukDesaController::class, 'index']);
    Route::get('/preview', [ProdukDesaController::class, 'preview']);
    Route::get('/{slug}', [ProdukDesaController::class, 'show']);
});

Route::prefix('comments')->group(function () {
    Route::get('/{slug}', [KomentarBeritaController::class, 'index']);
    Route::post('/', [KomentarBeritaController::class, 'store']);
    Route::get('/{slug}/replies/{commentId}', [KomentarBeritaController::class, 'getReplies']);
});

Route::prefix('produk-hukum')->group(function () {
    Route::get('/', [ProdukHukumController::class, 'index']);
    Route::get('/{slug}', [ProdukHukumController::class, 'show']);
});

Route::prefix('produk-hukum-comments')->group(function () {
    Route::get('/{slug}', [KomentarProdukHukumController::class, 'index']);
    Route::post('/', [KomentarProdukHukumController::class, 'store']);
    Route::get('/{slug}/replies/{commentId}', [KomentarProdukHukumController::class, 'getReplies']);
});

Route::prefix('feedback')->group(function () {
    Route::get('/kategori', [FeedbackController::class, 'getKategori']);
    Route::post('/', [FeedbackController::class, 'store']);
});

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/{event}/reminder', [EventController::class, 'setReminder']);
    Route::get('/preview', [EventController::class, 'preview']);
});

Route::prefix('demografi')->group(function () {
    Route::get('/', [DemografiController::class, 'index']);
    Route::get('/preview', [DemografiController::class, 'preview']);
    Route::get('/{slug}', [DemografiController::class, 'show']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
});
