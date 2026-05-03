<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\MeetingPointController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\WorkoutSessionController;
use App\Http\Controllers\WorkoutSignupController;
use App\Http\Controllers\WeatherViewController;

Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');

Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::resource('workouts', WorkoutController::class);

Route::resource('workout-sessions', WorkoutSessionController::class);

Route::patch('workout-sessions/{workoutSession}/cancel', [WorkoutSessionController::class, 'cancel'])
    ->name('workout-sessions.cancel');

Route::resource('meeting-points', MeetingPointController::class);

Route::resource('workout-signups', WorkoutSignupController::class);


//Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('weather/{location}', [WeatherViewController::class, 'show'])
        ->name('weather.view');
//});

Route::get('/', function () {
    Log::info('Root route accessed', [
        'authenticated' => auth()->check(),
        'nova_path' => Nova::path()
    ]);

    if (auth()->check()) {
        return redirect('/dashboards/main');
    }
    return redirect('/login');
});

Route::redirect('/home', '/dashboards/main');
