<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('customer')
    ->name('customer.')
    ->middleware('guest')
    ->group(function () {
        

    });