<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class MyUpcomingSessions extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $user = $this->dashboardUser($request);

        if (! $user) {
            return $this->result(0);
        }

        $count = $this->personalSignupQuery($user)
            ->whereDate('workout_sessions.session_date', '>=', now()->toDateString())
            ->whereIn('system_statuses.code', $this->activeSignupCodes())
            ->distinct('workout_signups.workout_session_id')
            ->count('workout_signups.workout_session_id');

        return $this->result($count)->help(__('Sessions you are currently signed up for.'));
    }

    public function name()
    {
        return __('My Upcoming Sessions');
    }
}
