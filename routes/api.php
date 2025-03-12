<?php

use App\Http\Controllers\API\RegionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('provinces', [RegionController::class, 'getProvinces']);
Route::get('regencies/{province_id}', [RegionController::class, 'getRegencies']);
Route::get('districts/{regency_id}', [RegionController::class, 'getDistricts']);
Route::get('villages/{district_id}', [RegionController::class, 'getVillages']);
