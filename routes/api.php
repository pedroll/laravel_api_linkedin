<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// curso master laravel api, login por /oauth/token
// rutas no seguras
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);

// rutas seguras
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('testOauth', [AuthController::class, 'testOauth']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');



