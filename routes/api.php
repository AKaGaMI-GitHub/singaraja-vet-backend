<?php

use App\Http\Controllers\API\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'Authenticated'], function () {

});

Route::post('/auth/registered', [RegisterController::class, 'registerAccount']);
Route::post('/auth/registered/{username}', [RegisterController::class, 'accountDetail']);
