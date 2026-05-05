<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\InterpretsSessionWindows;
use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedSessionWindowValue extends Value
{
    use InterpretsSessionWindows;
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $range = $request->range ?? 'THIS_WEEK';

        $query = $this->scopedSessionQuery($this->dashboardUser($request));
        $this->applySessionWindow($query, $range, 'workout_sessions.session_date');

        return $this->result($query->count());
    }

    public function ranges(): array
    {
        return $this->sessionWindowRanges();
    }

    public function name()
    {
        return __('Scoped Sessions');
    }

    public function cacheFor()
    {
        return now()->addMinutes(5);
    }
}
