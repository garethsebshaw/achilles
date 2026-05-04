<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SignupsNextWeek extends Value
{
    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $start = now()->addWeek()->startOfWeek()->toDateString();
        $end = now()->addWeek()->endOfWeek()->toDateString();

        return $this->result(
            WorkoutSignup::query()
                ->whereHas('workoutSession', function ($query) use ($start, $end) {
                    $query->whereBetween('session_date', [$start, $end]);
                })
                ->count()
        )->help(__('Signups tied to sessions happening next week.'));
    }

    public function name()
    {
        return __('Signups Next Week');
    }
}
