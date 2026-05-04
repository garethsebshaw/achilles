<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class MyAttendanceTrend extends Trend
{
    use ResolvesDashboardScope;

    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        $user = $this->dashboardUser($request);

        if (! $user) {
            return $this->result([]);
        }

        return $this->countByMonths(
            $request,
            $this->personalSignupQuery($user)
                ->whereIn('system_statuses.code', $this->attendedSignupCodes()),
            'workout_sessions.session_date'
        )->showLatestValue();
    }

    public function name()
    {
        return __('My Attendance Trend');
    }

    public function ranges()
    {
        return [
            30 => __('30 Days'),
            90 => __('90 Days'),
            365 => __('1 Year'),
        ];
    }
}
