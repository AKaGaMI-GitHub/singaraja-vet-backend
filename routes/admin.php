<?php

use App\Http\Controllers\Api\Admin\BlogController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'Authenticated', 'prefix' => 'admin'], function () {
    Route::post('/blog/store', [BlogController::class, 'store']);
});