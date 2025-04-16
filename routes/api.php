<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegionController;
use App\Http\Controllers\API\KomentarBeritaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('provinces', [RegionController::class, 'getProvinces']);
Route::get('regencies/{province_id}', [RegionController::class, 'getRegencies']);
Route::get('districts/{regency_id}', [RegionController::class, 'getDistricts']);
Route::get('villages/{district_id}', [RegionController::class, 'getVillages']);

Route::prefix('news')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\NewsController::class, 'index']);
    Route::get('/{news}', [App\Http\Controllers\Api\NewsController::class, 'show']);
});

Route::prefix('comments')->group(function () {
    Route::get('/{newsId}', [App\Http\Controllers\Api\KomentarBeritaController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\KomentarBeritaController::class, 'store']);
});
