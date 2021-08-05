<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::post('register', [UserController::class, 'register']);
Route::put('activate-account', [UserController::class, 'activate']);
Route::post('login', [UserController::class, 'login']);
Route::post('refresh', [UserController::class, 'refresh']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group( function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('send-invitation-link', [UserController::class, 'send_invitation_link']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('update-profile', [UserController::class, 'update_profile']);
});
