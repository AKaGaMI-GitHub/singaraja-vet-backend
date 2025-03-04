<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/abc', function () {
    return 'test';
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
