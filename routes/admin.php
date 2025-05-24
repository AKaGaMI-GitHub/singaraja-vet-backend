<?php

use App\Http\Controllers\Api\Admin\BlogController;
use App\Http\Controllers\Api\Admin\UserSettingsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'VetCheck', 'prefix' => 'admin'], function () {
    Route::post('/blog/store', [BlogController::class, 'store']);

    Route::get('/user-settings', [UserSettingsController::class, 'index']);
    Route::post('/user-settings/storeOrUpdate', [UserSettingsController::class, 'storeOrUpdate']);
    Route::patch('/user-settings/status/{id}', [UserSettingsController::class, 'status']);
    Route::delete('/user-settings/delete/{id}', [UserSettingsController::class, 'destroy']);
});