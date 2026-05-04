<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SignupsThisWeek extends Value
{
    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        return $this->result(
            WorkoutSignup::query()
                ->whereHas('workoutSession', function ($query) {
                    $query->whereBetween('session_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
                })
                ->count()
        );
    }

    public function name()
    {
        return __('Signups This Week');
    }
}
