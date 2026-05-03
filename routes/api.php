<?php

use App\Http\Controllers\WeatherDataController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/nova-api/weather-data/location/{locationId}', [WeatherDataController::class, 'getLocationData']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/workout-sessions/{session}/users', [WorkoutSessionAttendanceController::class, 'getSessionUsers']);
    Route::post('/workout-sessions/{session}/check-in', [WorkoutSessionAttendanceController::class, 'checkIn']);
    Route::post('/workout-sessions/{session}/check-out', [WorkoutSessionAttendanceController::class, 'checkOut']);
    Route::post('/workout-sessions/{session}/cancel-check-in', [WorkoutSessionAttendanceController::class, 'cancelCheckIn']);
    Route::post('/workout-sessions/{session}/assign-guide', [WorkoutSessionAttendanceController::class, 'assignGuide']);
    Route::get('/workout-sessions/{session}/search-users', [WorkoutSessionAttendanceController::class, 'searchUsers']);
    Route::post('/workout-sessions/{session}/add-user', [WorkoutSessionAttendanceController::class, 'addUserToSession']);
});
