<?php

namespace App\Listeners;

use App\Events\CheckInUser;
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

        if ($signup->checked_in_at) {
            $signup->checked_in_at = null;
            $signup->save();
            Log::info("User Check-In Cancelled for {$signup->id}");
        } else {
            $signup->checked_in_at = Carbon::now();
            $signup->save();
            Log::info("User Checked In for {$signup->id}");
        }
    }
}
