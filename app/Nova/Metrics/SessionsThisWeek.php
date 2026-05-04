<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSession;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SessionsThisWeek extends Value
{
    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        return $this->result(
            WorkoutSession::query()
                ->whereBetween('session_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->count()
        );
    }

    public function name()
    {
        return __('Sessions This Week');
    }
}
