<?php
// routes/auth.php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::middleware('web')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login')
        ->middleware('guest');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout')
        ->middleware('auth');
});
