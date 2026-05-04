<?php

namespace App\Listeners;

use App\Events\CheckOutUser;
use App\Models\SystemStatus;
use App\Models\WorkoutSignup;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HandleCheckOut
{
    public function handle(CheckOutUser $event)
    {
        $signup = $event->signup;

        if (!$signup) {
            Log::error("Check-out event triggered but resource is missing.");
            return;
        }

        if (!$signup->checked_in_at) {
            Log::warning("Attempted to check out without checking in: {$signup->id}");
            return;
        }

        if ($signup->checked_out_at) {
            Log::warning("User already checked out: {$signup->id}");
            return;
        }

        $signup->checked_out_at = Carbon::now();
        $signup->status_id = SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_out', ['signup_attended', 'signup_checked_in']);
        $signup->save();
        Log::info("User Checked Out for {$signup->id}");
    }
}
