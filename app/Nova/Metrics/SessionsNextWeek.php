<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSession;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SessionsNextWeek extends Value
{
    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $start = now()->addWeek()->startOfWeek()->toDateString();
        $end = now()->addWeek()->endOfWeek()->toDateString();

        return $this->result(
            WorkoutSession::query()
                ->whereBetween('session_date', [$start, $end])
                ->count()
        )->help(__('Sessions scheduled next week.'));
    }

    public function name()
    {
        return __('Sessions Next Week');
    }
}
