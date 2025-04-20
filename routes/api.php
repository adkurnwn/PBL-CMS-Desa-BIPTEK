<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\LayoutController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\HomePageController;
use App\Http\Controllers\Api\ProdukHukumController;
use App\Http\Controllers\Api\ProfilePageController;
use App\Http\Controllers\Api\KomentarBeritaController;
use App\Http\Controllers\Api\KomentarProdukHukumController;

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
    Route::get('/{news}', [NewsController::class, 'show']);
});

Route::prefix('comments')->group(function () {
    Route::get('/{newsId}', [KomentarBeritaController::class, 'index']);
    Route::post('/', [KomentarBeritaController::class, 'store']);
    Route::get('/{newsId}/replies/{commentId}', [KomentarBeritaController::class, 'getReplies']);
});

Route::get('/produk-hukum', [ProdukHukumController::class, 'index']);
Route::get('/produk-hukum/{id}', [ProdukHukumController::class, 'show']);

Route::prefix('produk-hukum-comments')->group(function () {
    Route::get('/{dokumenId}', [KomentarProdukHukumController::class, 'index']);
    Route::post('/', [KomentarProdukHukumController::class, 'store']);
    Route::get('/{dokumenId}/replies/{commentId}', [KomentarProdukHukumController::class, 'getReplies']);
});

Route::prefix('feedback')->group(function () {
    Route::get('/kategori', [App\Http\Controllers\API\FeedbackController::class, 'getKategori']);
    Route::post('/', [App\Http\Controllers\API\FeedbackController::class, 'store']);
});
