<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSession;
use App\Nova\Metrics\Concerns\InterpretsSessionWindows;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SessionWindowValue extends Value
{
    use InterpretsSessionWindows;

    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        $range = $request->range ?? 'THIS_WEEK';

        $query = WorkoutSession::query();
        $this->applySessionWindow($query, $range);

        return $this->result($query->count());
    }

    public function ranges(): array
    {
        return $this->sessionWindowRanges();
    }

    public function name()
    {
        return __('Sessions');
    }

    public function cacheFor()
    {
        return now()->addMinutes(5);
    }
}
