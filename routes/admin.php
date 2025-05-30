<?php

use App\Http\Controllers\Api\Admin\BlogController;
use App\Http\Controllers\Api\Admin\LogActivityController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisHewanController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisKelaminController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterRasHewanController;
use App\Http\Controllers\Api\Admin\UserSettingsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'VetCheck', 'prefix' => 'admin'], function () {
    Route::post('/blog/store', [BlogController::class, 'store']);

    Route::get('/master/master-hewan/jenis-kelamin', [MasterJenisKelaminController::class, 'index']);
    Route::post('/master/master-hewan/jenis-kelamin/create', [MasterJenisKelaminController::class, 'store']);
    Route::patch('/master/master-hewan/jenis-kelamin/update/{id}', [MasterJenisKelaminController::class, 'update']);
    Route::patch('/master/master-hewan/jenis-kelamin/status/{id}', [MasterJenisKelaminController::class, 'status']);

    Route::get('/master/master-hewan/jenis-hewan', [MasterJenisHewanController::class, 'index']);
    Route::post('/master/master-hewan/jenis-hewan/create', [MasterJenishewanController::class, 'store']);
    Route::patch('/master/master-hewan/jenis-hewan/update/{id}', [MasterJenishewanController::class, 'update']);
    Route::patch('/master/master-hewan/jenis-hewan/status/{id}', [MasterJenishewanController::class, 'status']);

    Route::get('/master/master-hewan/ras-hewan', [MasterRasHewanController::class, 'index']);
    Route::post('/master/master-hewan/ras-hewan/create', [MasterRasHewanController::class, 'store']);
    Route::patch('/master/master-hewan/ras-hewan/update/{id}', [MasterRasHewanController::class, 'update']);
    Route::patch('/master/master-hewan/ras-hewan/status/{id}', [MasterRasHewanController::class, 'status']);

    Route::get('/user-settings', [UserSettingsController::class, 'index']);
    Route::post('/user-settings/storeOrUpdate', [UserSettingsController::class, 'storeOrUpdate']);
    Route::get('/user-settings/{username}', [UserSettingsController::class, 'edit']);
    Route::patch('/user-settings/status/{username}', [UserSettingsController::class, 'status']);
    Route::patch('/user-settings/vet-status/{username}', [UserSettingsController::class, 'vetStatus']);
    Route::delete('/user-settings/delete/{username}', [UserSettingsController::class, 'destroy']);

    Route::get('/log-activity', [LogActivityController::class, 'index']);
});