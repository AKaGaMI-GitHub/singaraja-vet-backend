<?php

use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisHewanController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterJenisKelaminController;
use App\Http\Controllers\Api\Admin\MasterData\MasterHewan\MasterRasHewanController;
use App\Http\Controllers\Api\Admin\UserSettingsController;
use App\Http\Controllers\API\Users\BlogController;
use App\Http\Controllers\Api\Users\DashboardController;
use App\Http\Controllers\Api\Users\ListPetsController;
use App\Http\Controllers\Api\Users\RekamMedisController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user'], function () {
    Route::get('/blog/{type}', [BlogController::class, 'getBlog']);
    Route::get('/blog/detail/{type}', [BlogController::class, 'getDetailBlog']);


    Route::group(['middleware' => 'Authenticated'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/get-user', [DashboardController::class, 'getUser']);
        Route::patch('/changeAccount', [DashboardController::class, 'changeAccount']);

        Route::get('/list-pets', [ListPetsController::class, 'index']);
        Route::post('/list-pets/create', [ListPetsController::class, 'store']);
        Route::get('/list-pets/show-list/{id}', [ListPetsController::class, 'showList']);
        Route::patch('/list-pets/update/{id}', [ListPetsController::class, 'update']);
        Route::delete('/list-pets/delete/{id}', [ListPetsController::class, 'destroy']);

        Route::get('/rekam-medis', [RekamMedisController::class, 'index']);

        Route::get('/master-hewan/jenis-kelamin/show-list', [MasterJenisKelaminController::class, 'showList']);
        Route::get('/master-hewan/jenis-hewan/show-list', [MasterJenisHewanController::class, 'showList']);
        Route::get('/master-hewan/jenis-ras-hewan/show-list/{jenis_hewan_id}', [MasterRasHewanController::class, 'showList']);

        Route::post('/blog/comment/{slug}', [BlogController::class, 'commentBlogParent']);
        Route::post('/blog/comment/{slug}/reply/{idParent}', [BlogController::class, 'commentBlogChildren']);
    });
});
