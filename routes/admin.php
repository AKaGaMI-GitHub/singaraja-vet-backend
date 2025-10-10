<?php

use App\Http\Controllers\Api\Admin\BlogController;
use App\Http\Controllers\Api\Admin\LogActivityController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisHewanController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisKelaminController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterRasHewanController;
use App\Http\Controllers\Api\Admin\MasterData\MasterObat\MasterJenisObatController;
use App\Http\Controllers\Api\Admin\MasterData\MasterObat\MasterObatController;
use App\Http\Controllers\Api\Admin\RekamMedisController;
use App\Http\Controllers\Api\Admin\UserSettingsController;
use App\Http\Controllers\Api\Users\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'VetCheck', 'prefix' => 'admin'], function () {
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/detail/{slug}', [BlogController::class, 'show']);
    Route::post('/blog/store', [BlogController::class, 'store']);
    Route::patch('/blog/update/{id}', [BlogController::class, 'update']);
    Route::delete('/blog/delete/{id}', [BlogController::class, 'destroy']);

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

    Route::get('/master/master-obat/jenis-obat', [MasterJenisObatController::class, 'index']);
    Route::post('/master/master-obat/jenis-obat/create', [MasterJenisObatController::class, 'store']);
    Route::patch('/master/master-obat/jenis-obat/update/{id}', [MasterJenisObatController::class, 'update']);
    Route::patch('/master/master-obat/jenis-obat/status/{id}', [MasterJenisObatController::class, 'status']);
    Route::get('/master/master-obat/jenis-obat/show-list', [MasterJenisObatController::class, 'showList']);

    Route::get('/master/master-obat/obat', [MasterObatController::class, 'index']);
    Route::post('/master/master-obat/obat/create', [MasterObatController::class, 'store']);
    Route::patch('/master/master-obat/obat/update/{id}', [MasterObatController::class, 'update']);
    Route::patch('/master/master-obat/obat/status/{id}', [MasterObatController::class, 'status']);
    Route::get('/master/master-obat/obat/show-list', [MasterObatController::class, 'showList']);

    Route::get('/user-settings', [UserSettingsController::class, 'index']);
    Route::post('/user-settings/storeOrUpdate', [UserSettingsController::class, 'storeOrUpdate']);
    Route::get('/user-settings/{username}', [UserSettingsController::class, 'edit']);
    Route::get('/user-settings/list/list-user', [UserSettingsController::class, 'showList']);
    Route::patch('/user-settings/status/{username}', [UserSettingsController::class, 'status']);
    Route::patch('/user-settings/vet-status/{username}', [UserSettingsController::class, 'vetStatus']);
    Route::delete('/user-settings/delete/{username}', [UserSettingsController::class, 'destroy']);

    Route::post('/rekam-medis/create', [RekamMedisController::class, 'store']);
    Route::patch('/rekam-medis/update/{id}', [RekamMedisController::class, 'update']);
    Route::delete('/rekam-medis/delete/{id}', [RekamMedisController::class, 'destroy']);

    Route::get('/chat/rooms', [ChatController::class, 'listRoom']);

    Route::get('/log-activity', [LogActivityController::class, 'index']);
});
