<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedUpcomingEvents extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $count = $this->scopedEventQuery($this->dashboardUser($request))
            ->where('start_date', '>=', now())
            ->count();

        return $this->result($count);
    }

    public function name()
    {
        return __('Scoped Upcoming Events');
    }
}
