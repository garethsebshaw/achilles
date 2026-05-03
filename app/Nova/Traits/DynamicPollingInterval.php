<?php

namespace App\Nova\Traits;

trait DynamicPollingInterval
{
    protected function getPollingInterval($sessionStartTime)
    {
        $now = now();
        $thirtyMinutesAgo = $now->copy()->subMinutes(30);

        // Check if we're within 30 minutes of session start
        if ($now->diffInMinutes($sessionStartTime, false) <= 30) {
            return 5; // Poll every 5 seconds during check-in
        }

        return 60; // Poll every minute otherwise
    }
}
