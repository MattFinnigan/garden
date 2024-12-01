<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CropHistoryController;
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//   return $request->user();
// });

Route::get('/plants', [PlantController::class, 'index']);
Route::get('/plants/{id}', [PlantController::class, 'show']);
Route::post('/plants', [PlantController::class, 'store']);
Route::post('/plants/{id}', [PlantController::class, 'update']);
Route::delete('/plants/{id}', [PlantController::class, 'destroy']);

Route::get('/crops', [CropController::class, 'index']);
Route::get('/crops/{id}', [CropController::class, 'show']);
Route::post('/crops', [CropController::class, 'store']);
Route::delete('/crops/{id}', [CropController::class, 'destroy']);

Route::post('/crop-history/create/{id}', [CropHistoryController::class, 'create']);
Route::post('/crop-history/{id}', [CropHistoryController::class, 'update']);
Route::delete('/crop-history/{id}', [CropHistoryController::class, 'destroy']);

Route::get('/plots', [LocationController::class, 'index']);
Route::get('/plots/{id}', [LocationController::class, 'show']);
Route::post('/plots', [LocationController::class, 'store']);
Route::post('/plots/{id}', [LocationController::class, 'update']);
Route::delete('/plots/{id}', [LocationController::class, 'destroy']);
