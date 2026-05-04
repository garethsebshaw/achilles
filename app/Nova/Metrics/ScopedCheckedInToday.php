<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedCheckedInToday extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $count = $this->scopedSignupQuery($this->dashboardUser($request))
            ->whereDate('workout_signups.checked_in_at', now()->toDateString())
            ->distinct('workout_signups.id')
            ->count('workout_signups.id');

        return $this->result($count);
    }

    public function name()
    {
        return __('Scoped Check-Ins Today');
    }
}
