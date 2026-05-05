<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\InterpretsSessionWindows;
use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedSignupWindowValue extends Value
{
    use InterpretsSessionWindows;
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $range = $request->range ?? 'THIS_WEEK';

        $query = $this->scopedSignupQuery($this->dashboardUser($request))
            ->whereIn('system_statuses.code', $this->activeSignupCodes());

        $this->applySessionWindow($query, $range, 'workout_sessions.session_date');

        return $this->result($query->distinct('workout_signups.id')->count('workout_signups.id'));
    }

    public function ranges(): array
    {
        return $this->sessionWindowRanges();
    }

    public function name()
    {
        return __('Scoped Signups');
    }

    public function cacheFor()
    {
        return now()->addMinutes(5);
    }
}
