<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\NewsController;
use App\Http\Controllers\api\EventController;
use App\Http\Controllers\api\LayoutController;
use App\Http\Controllers\api\RegionController;
use App\Http\Controllers\api\FeedbackController;
use App\Http\Controllers\api\HomePageController;
use App\Http\Controllers\api\ProdukHukumController;
use App\Http\Controllers\api\ProfilePageController;
use App\Http\Controllers\api\KomentarBeritaController;
use App\Http\Controllers\api\KomentarProdukHukumController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('provinces', [RegionController::class, 'getProvinces']);
Route::get('regencies/{province_id}', [RegionController::class, 'getRegencies']);
Route::get('districts/{regency_id}', [RegionController::class, 'getDistricts']);
Route::get('villages/{district_id}', [RegionController::class, 'getVillages']);

Route::get('profile', [ProfilePageController::class, 'index']);
Route::get('layout', [LayoutController::class, 'index']);
Route::get('home', [HomePageController::class, 'index']);

Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/{slug}', [NewsController::class, 'show']);
});

Route::prefix('comments')->group(function () {
    Route::get('/{slug}', [KomentarBeritaController::class, 'index']);
    Route::post('/', [KomentarBeritaController::class, 'store']);
    Route::get('/{slug}/replies/{commentId}', [KomentarBeritaController::class, 'getReplies']);
});

Route::get('/produk-hukum', [ProdukHukumController::class, 'index']);
Route::get('/produk-hukum/{id}', [ProdukHukumController::class, 'show']);

Route::prefix('produk-hukum-comments')->group(function () {
    Route::get('/{dokumenId}', [KomentarProdukHukumController::class, 'index']);
    Route::post('/', [KomentarProdukHukumController::class, 'store']);
    Route::get('/{dokumenId}/replies/{commentId}', [KomentarProdukHukumController::class, 'getReplies']);
});

Route::prefix('feedback')->group(function () {
    Route::get('/kategori', [FeedbackController::class, 'getKategori']);
    Route::post('/', [FeedbackController::class, 'store']);
});

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/{event}/reminder', [EventController::class, 'setReminder']);
});