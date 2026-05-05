<?php

use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use App\Support\Attendance\CheckInSessionContext;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$context = $app->make(CheckInSessionContext::class);

$session = WorkoutSession::query()
    ->has('signups')
    ->with(['signups.user'])
    ->whereBetween('session_date', [
        now()->subDay()->toDateString(),
        now()->addDay()->toDateString(),
    ])
    ->orderBy('session_date')
    ->orderBy('start_time')
    ->get()
    ->first(fn (WorkoutSession $candidate) => $context->isSessionWithinCheckInWindow($candidate))
    ?? WorkoutSession::query()
        ->has('signups')
        ->with(['signups.user'])
        ->get()
        ->first(fn (WorkoutSession $candidate) => $context->isSessionWithinCheckInWindow($candidate))
    ?? WorkoutSession::query()
        ->has('signups')
        ->with(['signups.user'])
        ->orderByRaw('ABS(julianday(session_date) - julianday(?))', [now()->toDateString()])
        ->orderBy('start_time')
        ->firstOrFail();

$guideSignup = WorkoutSignup::query()
    ->where('workout_session_id', $session->id)
    ->whereNotNull('athlete_id')
    ->with('user')
    ->first()
    ?? WorkoutSignup::query()
        ->where('workout_session_id', $session->id)
        ->with('user')
        ->firstOrFail();

$searchableSignup = $session->signups
    ->first(fn ($signup) => $signup->user && filled($signup->user->name))
    ?? $guideSignup;

echo json_encode([
    'sessionId' => $session->id,
    'signupId' => $guideSignup->id,
    'userId' => $searchableSignup->user_id,
    'userSearch' => $searchableSignup->user?->name,
], JSON_THROW_ON_ERROR);
