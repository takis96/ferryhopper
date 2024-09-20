<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FerryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/itineraries', [FerryController::class, 'getItineraries']);
Route::post('/prices', [FerryController::class, 'getPrices']);
