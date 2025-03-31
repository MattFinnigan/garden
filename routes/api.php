<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\CropEntryController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\ImageUploadController;
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//   return $request->user();
// });

Route::get('/plants', [PlantController::class, 'index']);
Route::get('/plants/{id}', [PlantController::class, 'show']);
Route::post('/plants', [PlantController::class, 'store']);
Route::post('/plants/{id}', [PlantController::class, 'update']);
Route::delete('/plants/{id}', [PlantController::class, 'destroy']);

Route::get('/crops', [CropController::class, 'index']);
Route::get('/crops/month/{month}', [CropController::class, 'byMonth']);
Route::get('/crops/{id}', [CropController::class, 'show']);
Route::post('/crops/{id}', [CropController::class, 'update']);
Route::post('/crops', [CropController::class, 'store']);
Route::delete('/crops/{id}', [CropController::class, 'destroy']);

Route::post('/crop-entry/create/{id}', [CropEntryController::class, 'store']);
Route::post('/crop-entry/{id}', [CropEntryController::class, 'update']);
Route::delete('/crop-entry/{id}', [CropEntryController::class, 'destroy']);

Route::get('/beds', [BedController::class, 'index']);
Route::get('/beds/{id}', [BedController::class, 'show']);
Route::post('/beds', [BedController::class, 'store']);
Route::post('/beds/{id}', [BedController::class, 'update']);
Route::delete('/beds/{id}', [BedController::class, 'destroy']);


Route::post('/image-upload', [ImageUploadController::class, 'imageUploadPost']);
