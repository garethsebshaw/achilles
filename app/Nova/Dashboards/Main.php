<?php

namespace App\Nova\Dashboards;

use App\Models\User;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(): array
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return [];
        }

        $cards = [];

        if ($user->hasPrivilegedRole()) {
            $cards = array_merge($cards, $this->operationsCards());
        }

        if ($user->is_athlete || $user->is_guide || $cards === []) {
            $cards = array_merge($cards, $this->personalCards());
        }

        return $cards;
    }

    private function operationsCards(): array
    {
        return [
            new \App\Nova\Metrics\ScopedChapterCount(),
            new \App\Nova\Metrics\ScopedLocationCount(),
            new \App\Nova\Metrics\ScopedUpcomingSessions(),
            new \App\Nova\Metrics\ScopedTodaySessions(),
            new \App\Nova\Metrics\ScopedUpcomingSignups(),
            new \App\Nova\Metrics\ScopedCheckedInToday(),
            new \App\Nova\Metrics\ScopedOpenMaintenanceRequests(),
            new \App\Nova\Metrics\ScopedUpcomingEvents(),
            new \App\Nova\Metrics\ScopedSignupStatusBreakdown(),
            new \App\Nova\Metrics\ScopedSessionTrend(),
        ];
    }

    private function personalCards(): array
    {
        return [
            new \App\Nova\Metrics\MyUpcomingSessions(),
            new \App\Nova\Metrics\MyAttendedSessions(),
            new \App\Nova\Metrics\MyMissedSessions(),
            new \App\Nova\Metrics\MyPartnerCount(),
            new \App\Nova\Metrics\MySportBreakdown(),
            new \App\Nova\Metrics\MyAttendanceTrend(),
        ];
    }
}
