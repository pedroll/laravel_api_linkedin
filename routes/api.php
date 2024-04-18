<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserdataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// curso master laravel api, login por /oauth/token
// rutas no seguras
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);
//recupera userdatas
Route::get('getUsers', [UserController::class, 'index']);
// rutas seguras
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('testOauth', [AuthController::class, 'testOauth']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    //recupera userdatas
    Route::get('getUserdatas', [UserdataController::class, 'getUserdatas']);
    Route::get('getUserdata/{id}', [UserdataController::class, 'getUserdataDetail']);

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');



