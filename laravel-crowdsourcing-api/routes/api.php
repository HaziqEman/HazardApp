<?php

use App\Http\Controllers\Api\HazardController;
use Illuminate\Support\Facades\Route;

Route::get('/hazards', [HazardController::class, 'index']);
Route::post('/hazards', [HazardController::class, 'store']);
Route::get('/hazards/{hazard}', [HazardController::class, 'show']);
Route::delete('/hazards/{hazard}', [HazardController::class, 'destroy']);
