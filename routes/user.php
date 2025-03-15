<?php

use App\Http\Controllers\API\Users\BlogController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user'], function () {
    Route::get('/blog/{type}', [BlogController::class, 'getBlog']);
});