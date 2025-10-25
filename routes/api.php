<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StatusController;

Route::get('', function () {
    return response()->json(['message' => 'Welcome to the Country API']);
});

Route::post('/countries/refresh', [CountryController::class, 'refresh']);
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/image', [CountryController::class, 'image']);
Route::get('/countries/{name}', [CountryController::class, 'show']);
Route::delete('/countries/{name}', [CountryController::class, 'destroy']);
Route::get('/status', [StatusController::class, 'show']);

