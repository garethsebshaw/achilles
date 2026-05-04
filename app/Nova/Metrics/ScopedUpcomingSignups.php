<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedUpcomingSignups extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $count = $this->scopedSignupQuery($this->dashboardUser($request))
            ->whereDate('workout_sessions.session_date', '>=', now()->toDateString())
            ->whereIn('system_statuses.code', $this->activeSignupCodes())
            ->count();

        return $this->result($count);
    }

    public function name()
    {
        return __('Scoped Upcoming Signups');
    }
}
