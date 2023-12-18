<?php

use App\Http\Controllers\API\VerwaltenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->group(function () {
    Route::post('create-user', 'createUser');
    Route::post('login', 'loginUser');
});

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'getUserDetail');
    Route::get('logout', 'userLogout');
    Route::get('users-list', 'usersList');

})->middleware('auth:api');


Route::middleware('auth:api')->group(function () {
    Route::apiResource('verwalten', VerwaltenController::class);
    Route::post('verwalten/{verwalten}/abziehen', [VerwaltenController::class, 'abziehen'])->name('verwalten.abziehen');
});
