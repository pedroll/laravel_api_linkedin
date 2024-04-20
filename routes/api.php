<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserdataController;
use Illuminate\Support\Facades\Route;

// curso master laravel api, login por /oauth/token
// rutas no seguras
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);

// rutas seguras
Route::group(['middleware' => 'auth:api'], function () {
//    Route::get('user', [UserdataController::class, 'getUserdatas']);
//    Route::get('/user/{id}', [UserdataController::class, 'getUserdataDetail']);
//    Route::post('user', [UserdataController::class, 'store']);
//    Route::put('user', [UserdataController::class, 'update']);
//    Route::delete('user', [UserdataController::class, 'destroy']);
    //routes resource userdata
    Route::resource('user', UserdataController::class);

    Route::post('testOauth', [AuthController::class, 'testOauth']);
    Route::post('refresh', [AuthController::class, 'refresh']);

});






