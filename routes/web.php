<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccountSecurityController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\MeetingPointController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\WorkoutSessionController;
use App\Http\Controllers\WorkoutSignupController;
use App\Http\Controllers\WeatherViewController;
use App\Http\Controllers\CheckInStaffSessionController;

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

Route::middleware('auth')->group(function () {
    Route::get('account/security', [AccountSecurityController::class, 'show'])
        ->name('account.security');

    Route::put('account/security/password', [AccountSecurityController::class, 'updatePassword'])
        ->name('account.security.password.update');

    Route::post('account/security/two-factor', [AccountSecurityController::class, 'enableTwoFactor'])
        ->name('account.security.two-factor.enable');

    Route::post('account/security/two-factor/confirm', [AccountSecurityController::class, 'confirmTwoFactor'])
        ->name('account.security.two-factor.confirm');

    Route::post('account/security/two-factor/recovery-codes', [AccountSecurityController::class, 'regenerateRecoveryCodes'])
        ->name('account.security.two-factor.recovery-codes');

    Route::delete('account/security/two-factor', [AccountSecurityController::class, 'disableTwoFactor'])
        ->name('account.security.two-factor.disable');

    Route::get('attendance/sessions/{session}/activate', [CheckInStaffSessionController::class, 'activate'])
        ->name('attendance.sessions.activate');

    Route::get('attendance/sessions/deactivate', [CheckInStaffSessionController::class, 'deactivate'])
        ->name('attendance.sessions.deactivate');

    Route::get('attendance/sessions/weather/refresh', [CheckInStaffSessionController::class, 'refreshWeather'])
        ->name('attendance.sessions.weather.refresh');

    Route::get('attendance/users/{user}/check-in', [CheckInStaffSessionController::class, 'checkInUser'])
        ->name('attendance.users.check-in');

    Route::get('attendance/users/{user}/check-out', [CheckInStaffSessionController::class, 'checkOutUser'])
        ->name('attendance.users.check-out');
});


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
