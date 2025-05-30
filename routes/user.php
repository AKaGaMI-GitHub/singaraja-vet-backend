<?php

use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisHewanController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisKelaminController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterRasHewanController;
use App\Http\Controllers\API\Users\BlogController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user'], function () {
    Route::get('/blog/{type}', [BlogController::class, 'getBlog']);
    Route::get('/blog/detail/{type}', [BlogController::class, 'getDetailBlog']);

    Route::get('/master-hewan/jenis-kelamin/show-list', [MasterJenisKelaminController::class, 'showList']);
    Route::get('/master-hewan/jenis-hewan/show-list', [MasterJenisHewanController::class, 'showList']);
    Route::get('/master-hewan/jenis-ras-hewan/show-list/{jenis_hewan_id}', [MasterRasHewanController::class, 'showList']);
});