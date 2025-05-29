<?php

use App\Http\Controllers\Api\Admin\BlogController;
use App\Http\Controllers\Api\Admin\UserSettingsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'VetCheck', 'prefix' => 'admin'], function () {
    Route::post('/blog/store', [BlogController::class, 'store']);

    Route::get('/user-settings', [UserSettingsController::class, 'index']);
    Route::post('/user-settings/storeOrUpdate', [UserSettingsController::class, 'storeOrUpdate']);
    Route::get('/user-settings/{username}', [UserSettingsController::class, 'edit']);
    Route::patch('/user-settings/status/{username}', [UserSettingsController::class, 'status']);
    Route::patch('/user-settings/vet-status/{username}', [UserSettingsController::class, 'vetStatus']);
    Route::delete('/user-settings/delete/{username}', [UserSettingsController::class, 'destroy']);
});