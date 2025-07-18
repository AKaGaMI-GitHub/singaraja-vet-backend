<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\Api\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/image-checker/{path}', [ImageController::class, 'checkImage'])->where('path', '.*');;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/login-mobile', [AuthController::class, 'loginMobile']);
Route::middleware('Authenticated')->post('/logout', [AuthController::class, 'logout']);

Route::get('/login/redirect/{provider}', [AuthController::class, 'redirectToProvider']);

//Callback SSO
Route::get('/login/callback/google', [AuthController::class, 'handleGoogleCallback']);

Route::post('/auth/registered', [RegisterController::class, 'registerAccount']);
Route::post('/auth/registered/detail', [RegisterController::class, 'accountDetail']);

require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';
