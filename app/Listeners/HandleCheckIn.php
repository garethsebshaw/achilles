<?php

namespace App\Listeners;

use App\Events\CheckInUser;
use App\Models\SystemStatus;
use App\Models\WorkoutSignup;
use App\Support\Attendance\CheckInSessionContext;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HandleCheckIn
{
    public function handle(CheckInUser $event)
    {
        $signup = $event->signup;

        if (!$signup) {
            Log::error("Check-in event triggered but resource is missing.");
            return;
        }

        $activeSessionId = app(CheckInSessionContext::class)->currentSessionId();

        if ((int) $activeSessionId !== (int) $signup->workout_session_id) {
            Log::warning("Attempted to check in user outside active staff session: {$signup->id}");
            return;
        }

        if ($signup->checked_in_at) {
            $signup->checked_in_at = null;
            $signup->checked_out_at = null;
            $signup->status_id = SystemStatus::idForModel(WorkoutSignup::class, 'signup_confirmed', ['signup_pending']);
            $signup->save();
            Log::info("User Check-In Cancelled for {$signup->id}");
        } else {
            $signup->checked_in_at = Carbon::now();
            $signup->status_id = SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_in', ['signup_confirmed', 'signup_pending']);
            $signup->save();
            Log::info("User Checked In for {$signup->id}");
        }
    }
}
