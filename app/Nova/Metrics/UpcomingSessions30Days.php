<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSession;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class UpcomingSessions30Days extends Value
{
    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        return $this->result(
            WorkoutSession::query()
                ->whereBetween('session_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                ->count()
        )->help(__('Sessions scheduled over the next 30 days.'));
    }

    public function name()
    {
        return __('Upcoming Sessions (30 Days)');
    }
}
