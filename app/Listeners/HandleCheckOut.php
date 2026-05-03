<?php

namespace App\Listeners;

use App\Events\CheckOutUser;
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
        $signup->save();
        Log::info("User Checked Out for {$signup->id}");
    }
}
