<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class ScopedSessionTrend extends Trend
{
    use ResolvesDashboardScope;

    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        return $this->countByDays(
            $request,
            $this->scopedSessionQuery($this->dashboardUser($request)),
            'workout_sessions.session_date'
        )->showLatestValue();
    }

    public function name()
    {
        return __('Scoped Session Trend');
    }

    public function ranges()
    {
        return [
            7 => __('7 Days'),
            30 => __('30 Days'),
            90 => __('90 Days'),
        ];
    }
}
