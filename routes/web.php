<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('notfound');
});

require __DIR__.'/auth.php';
