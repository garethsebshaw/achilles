<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/', function (Request $request) {
//     //
// });

Route::middleware(['nova'])
    ->prefix('nova-vendor/workout-management')
    ->group(function () {
        Route::get('/sessions/{session}/users', [WorkoutSessionAttendanceController::class, 'getSessionUsers']);
        Route::post('/sessions/{session}/check-in', [WorkoutSessionAttendanceController::class, 'checkIn']);
        Route::post('/sessions/{session}/check-out', [WorkoutSessionAttendanceController::class, 'checkOut']);
        Route::post('/sessions/{session}/cancel-check-in', [WorkoutSessionAttendanceController::class, 'cancelCheckIn']);
    });
